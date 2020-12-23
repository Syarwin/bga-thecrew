var isDebug = window.location.host == 'studio.boardgamearena.com' || window.location.hash.indexOf('debug') > -1;
var debug = isDebug ? console.info.bind(window.console) : function () { };


define(["dojo", "dojo/_base/declare","ebg/core/gamegui",], (dojo, declare) => {
  return declare("customgame.game", ebg.core.gamegui, {
    /*
     * Constructor
     */
    constructor() {
      this._notifications = [];
      this._activeStates = [];
      this._connections = [];
    },



    /*
     * [Undocumented] Override BGA framework functions to call onLoadingComplete when loading is done
     */
    setLoader(value,max){
      this.inherited(arguments);
      if (!this.isLoadingComplete && value >= 100) {
        this.isLoadingComplete = true;
        this.onLoadingComplete();
      }
    },

    onLoadingComplete(){
      debug('Loading complete');
    },




    /*
     * Setup:
     */
    setup(gamedatas) {
      // Create a new div for buttons to avoid BGA auto clearing it
      dojo.place("<div id='customActions' style='display:inline-block'></div>", $("generalactions"), "after");

      this.setupNotifications();
      this.initPreferencesObserver();
     },


     /*
 		  * Detect if spectator or replay
 		  */
     isReadOnly() {
       return this.isSpectator || typeof g_replayFrom != 'undefined' || g_archive_mode;
     },


     /*
      * Chain format block with dojo.place
      */
     place(template,data,container){
       dojo.place(this.format_block(template, data), container);
     },


     /*
 		 * Make an AJAX call with automatic lock
 		 */
     takeAction(action, data, reEnterStateOnError) {
       data = data || {};
       data.lock = true;
       let promise = new Promise((resolve, reject) => {
         this.ajaxcall(
           "/" + this.game_name + "/" + this.game_name + "/" + action + ".html",
          data,
          this,
          (data) => resolve(data),
          (isError,message,code) => {
            if(isError)
              reject(message, code);
          });
       });

       if(reEnterStateOnError){
         promise.catch(() => this.onEnteringState(this.gamedatas.gamestate.name, this.gamedatas.gamestate) );
       }

       return promise;
     },


     /*
      * onEnteringState:
      * 	this method is called each time we are entering into a new game state.
      *
      * params:
      *  - str stateName : name of the state we are entering
      *  - mixed args : additional information
      */
     onEnteringState(stateName, args) {
       debug('Entering state: ' + stateName, args);

       if (this._activeStates.includes(stateName) && !this.isCurrentPlayerActive()) return;

       // Call appropriate method
       var methodName = "onEnteringState" + stateName.charAt(0).toUpperCase() + stateName.slice(1);
       if (this[methodName] !== undefined)
         this[methodName](args.args);
     },



     /*
      * onLeavingState:
      * 	this method is called each time we are leaving a game state.
      *
      * params:
      *  - str stateName : name of the state we are leaving
      */
     onLeavingState(stateName) {
       debug('Leaving state: ' + stateName);
       this.clearPossible();
     },
     clearPossible(){
       this.removeActionButtons();
       dojo.empty("customActions");

       this._connections.forEach(dojo.disconnect);
       this._connections = [];
     },


     /*
      * Custom connect that keep track of all the connections
      */
     connect(node, action, callback){
       this._connections.push(dojo.connect(node, action, callback));
     },


     /*
      * setupNotifications
      */
     setupNotifications() {
       console.log(this._notifications);
       this._notifications.forEach(notif => {
         var functionName = "notif_" + notif[0];

         dojo.subscribe(notif[0], this, functionName);
         if(notif[1] != null){
           this.notifqueue.setSynchronous(notif[0], notif[1]);

           // xxxInstant notification runs same function without delay
           dojo.subscribe(notif[0] + 'Instant', this, functionName);
           this.notifqueue.setSynchronous(notif[0] + 'Instant', 10);
         }
       });
     },



     /*
      * Add a timer on an action button :
      * params:
      *  - buttonId : id of the action button
      *  - time : time before auto click
      *  - pref : 0 is disabled (auto-click), 1 if normal timer, 2 if no timer and show normal button
      */

     startActionTimer(buttonId, time, pref) {
       var button = $(buttonId);
       var isReadOnly = this.isReadOnly();
       if (button == null || isReadOnly || pref == 2) {
         debug('Ignoring startActionTimer(' + buttonId + ')', 'readOnly=' + isReadOnly, 'prefValue=' + prefValue);
         return;
       }

       // If confirm disabled, click on button
       if (pref == 0) {
         button.click();
         return;
       }

       this._actionTimerLabel = button.innerHTML;
       this._actionTimerSeconds = time;
       this._actionTimerFunction = () => {
         var button = $(buttonId);
         if (button == null) {
           this.stopActionTimer();
         } else if (this._actionTimerSeconds-- > 1) {
           button.innerHTML = this._actionTimerLabel + ' (' + this._actionTimerSeconds + ')';
         } else {
           debug('Timer ' + buttonId + ' execute');
           button.click();
         }
       };
       this._actionTimerFunction();
       this._actionTimerId = window.setInterval(this._actionTimerFunction, 1000);
       debug('Timer #' + this._actionTimerId + ' ' + buttonId + ' start');
     },

     stopActionTimer() {
       if (this._actionTimerId != null) {
         debug('Timer #' + this._actionTimerId + ' stop');
         window.clearInterval(this._actionTimerId);
         delete this._actionTimerId;
       }
     },




     /*
      * Play a given sound that should be first added in the tpl file
      */
     playSound(sound, playNextMoveSound = true) {
       playSound(sound);
       playNextMoveSound && this.disableNextMoveSound();
     },


     /*
      * Add a blue/grey button if it doesn't already exists
      */
     addPrimaryActionButton(id, text, callback){
       if(!$(id))
        this.addActionButton(id, text, callback, "customActions", false, 'blue');
     },

     addSecondaryActionButton(id, text, callback){
       if(!$(id))
        this.addActionButton(id, text, callback, "customActions", false, 'gray');
     },

     addDangerActionButton(id, text, callback){
       if(!$(id))
        this.addActionButton(id, text, callback, "customActions", false, 'red');
     },


     /*
      * Preference polyfill
      */
     setPreferenceValue(number, newValue) {
 			var optionSel = 'option[value="' + newValue + '"]'
 			dojo.query('#preference_control_' + number + ' > ' +	optionSel
         +	', #preference_fontrol_' +number + ' > ' +	optionSel)
 				.attr('selected', true)
 			var select = $('preference_control_' + number)
 			if (dojo.isIE) {
 				select.fireEvent('onchange')
 			} else {
 				var event = document.createEvent('HTMLEvents')
 				event.initEvent('change', false, true)
 				select.dispatchEvent(event)
 			}
 		},

 		initPreferencesObserver() {
 			dojo.query('.preference_control').on(
 				'change', (e) => {
 					var match = e.target.id.match(/^preference_control_(\d+)$/)
 					if (!match) {
 						return
 					}
 					var pref = match[1]
 					var newValue = e.target.value
 					this.prefs[pref].value = newValue
 					this.onPreferenceChange(pref, newValue)
 			})
 		},

    onPreferenceChange(pref, newValue){
    },


    /*
     * Sliding animation wrappers with Promises
     */
    slide(sourceId, targetId, duration, delay = 0) {
      return new Promise((resolve, reject) => {
        var animation = this.slideToObject(sourceId, targetId, duration, delay);
        dojo.connect(animation, 'onEnd', resolve);
        animation.play();
      });
    },

    slideAndDestroy(sourceId, targetId, duration, delay = 0) {
    	return new Promise((resolve, reject) => {
        this.slideToObjectAndDestroy(sourceId, targetId, duration, delay);
    		setTimeout(() => {
    			resolve();
    		}, duration + delay)
    	});
    },

    slideTemporary(template, data, container, sourceId, targetId, duration, delay = 0) {
    	return new Promise((resolve, reject) => {
    		var animation = this.slideTemporaryObject(this.format_block(template, data), container, sourceId, targetId, duration, delay);
    		setTimeout(() => {
    			resolve();
    		}, duration + delay)
    	});
    },



    /*
     * Return a span with a colored 'You'
     */
    coloredYou() {
        var color = this.gamedatas.players[this.player_id].color;
        var color_bg = "";
        if (this.gamedatas.players[this.player_id] && this.gamedatas.players[this.player_id].color_back) {
            color_bg = "background-color:#" + this.gamedatas.players[this.player_id].color_back + ";";
        }
        var you = "<span style=\"font-weight:bold;color:#" + color + ";" + color_bg + "\">" + __("lang_mainsite", "You") + "</span>";
        return you;
    },
  });
});
