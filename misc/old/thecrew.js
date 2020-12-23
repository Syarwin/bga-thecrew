/**
 *------
 * BGA framework: Â© Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * thecrew implementation : Â© Nicolas Gocel <nicolas.gocel@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * thecrew.js
 *
 * thecrew user interface script
 *
 * In this file, you are describing the logic of your user interface, in Javascript language.
 *
 */

define([
    "dojo","dojo/_base/declare",
    "ebg/core/gamegui",
    "ebg/counter",
    "ebg/stock",
],
function (dojo, declare) {
    return declare("bgagame.thecrew", ebg.core.gamegui, {
        constructor: function(){

            // Here, you can init the global variables of your user interface
            // Example:
            this.commander_id = null;
            this.players = null;
            this.colors = null;

            this.card_width = 100;
            this.card_height = 157;

            // Number of turns
            this.mission_counter = null;
            this.attempts_counter = null;
            this.total_attempts_counter = null;
            this.trick_counters = {};
            this.cards_counters = {};
            this.multiSelect = null;
            this.selected = null;
            this.stateName = null;

            this.task_markers = {
                	'1' :  _('This task must be fulfilled first.'),
                	'2' :  _('This task must be fulfilled second.'),
                	'3' :  _('This task must be fulfilled third.'),
                	'4' :  _('This task must be fulfilled fourth.'),
                	'5' :  _('This task must be fulfilled fifth.'),
                	'o' :  _('This task must be fulfilled last.'),
                	'i1' :  _('This task must be fulfilled before &rsaquo;&rsaquo;.'),
                	'i2' :  _('This task must be fulfilled after &rsaquo;.'),
                	'i3' :  _('This task must be fulfilled after &rsaquo;&rsaquo;.'),
                	'i4' :  _('This task must be fulfilled after &rsaquo;&rsaquo;&rsaquo;.'),
            };

            this.missions = {1 : _('Congratulations! You have been chosen from a vast array of applicants to participate in the most important, and dangerous adventure that mankind has ever faced: the search for Planet Nine. You barely arrive at the training facility before you have already begun your first training phase: team building.'),
            		2 : _('It is apparent that you are a perfectly matched crew. Above all, is your mental connection — this so-called drift compatibility bodes well for an ongoing successful collaboration. It’s time for training phases two and three: control technology and weightlessness.'),
            		3 : _('The training phases each build on the lessons learned in the previous phase. This combined energy supply and emergency prioritization course will require a high degree of logical thinking to understand and make the appropriate connections. Your education in mathematics will certainly come in handy for this.'),
            		4 : _('You are nearing completion of the preparation phase. These last training phases are focused on the recalibration of the control modules, and the reorientation of the communicators and the advanced auxiliary systems on the spacesuits. You should be ready to launch soon. '),
            		5 : _('Celebrated too soon! One of you is sick in bed. <b>After everyone has looked at his or her cards, your commander asks everyone how he or she feels. It may only be answered with “good” or “bad.” Your commander then decides who is ill. The sick crew member must not win any tricks.</b>'),
            		6 : _('After this minor setback, your final training phase lies ahead: Learning what to do in the case of restricted communication. In order to do so, a <b>dead zone</b> will be simulated, these can occur in space for a number of reasons, so it’s important to train for them. Strengthen your mental connections to pass the final tests. '),
            		7 : _('This will be a memorable day! You are now prepared for launch. The completion of training, however, is only just the beginning of your adventure. 10-9-8-7-6-5-4-3-2-1-LIFT OFF! A massive force presses you into your seats — now there is no going back. With a deafening noise, you leave the ground, the country, the continent, and the planet. '),
            		8 : _('You have reached lunar orbit, weightlessness sets in — it’s an indescribable feeling. You’ve completed numerous tests and training for this moment and yet joy overwhelms you every time. You look back at Earth, which was your entire cosmos so far and already you can cover it with just your thumb. '),
            		9 : _('You are abruptly torn from your thoughts, as the onboard analysis module NAVI deafeningly sounds an alarm, demanding your attention. A tiny piece of metal has become wedged in the electronic drive unit. A steady hand will be needed in order to not damage the circuit boards. <b>At least one color card with a value of 1 must win a trick.</b>'),
            		10 : _('After all this excitement right at the beginning of your mission you are now ready to leave the moon behind. You send your status back to Earth, activate the flight control and measurements instruments, and ignite the engines. This will truly be one giant leap. For you and for humankind. '),
            		11 : _('Your radar systems report a dense meteorite field that will soon intercept your course. <b>The commander designates a crew member who must complete the recalculation of the course. This task will require a high level of concentration, thus the chosen crew member must not communicate during this part of the mission. </b>'),
            		12 : _('You watch tensely as you pass remarkably close to the meteorites. In all of the excitement, you have messed up your records, causing a few minutes of confusion. <b>Immediately after the first trick, each of you must draw a random card from the crew member to your right and add it to your hand. Then continue playing normally.</b> '),
            		13 : _('You have been hit by some small space debris despite having previously altered course. The control modules are indicating a malfunction in the drive. You will need to perform a thrust test on all drives to further isolate the problem. <b>You have to win a trick with each rocket card.</b>'),
            		14 : _('You are now already close enough to Mars that you can see Olympus Mons, the highest volcano in our solar system, with the naked eye. You use this opportunity to first photograph it and then the Mars moons Phobos and Deimos. The sight makes up for the fact that right now you’re in a <b>dead zone</b>. '),
            		15 : _('You pass Mars and exit the dead zone. Your thoughts start to randomly drift to chocolate bars when suddenly your collision module sounds an alarm. Before you can even react appropriately, you are hit by a meteoroid. Immediately isolate the four damaged modules and begin making repairs. '),
            		16 : _('Overall, the shock was worse than the actual damage, and you were able to fix most of it in a timely manner. However, the ninth control module, which monitors your suits’ life support systems, has been severely damaged in the collision and has failed. <b>You must not win a trick with a nine.</b>'),
            		17 : _('The damage to the ninth control module is even worse than originally thought, so you will have to invest significantly more time in making the repair. At the same time, however, you still have to track your course and send a message back to Earth where they are eagerly awaiting your status. <b>You still must not win a trick with a nine. </b>'),
            		18 : _('You set course for Jupiter, as you fly into a dust cloud. At almost the same time there is something strange going on with your communication module. It initially indicates there’s an astonishingly good connection, but only seconds later it appears to be in a total blackout. <b>You are only allowed to communicate from the second trick on. </b>'),
            		19 : _('The dust cloud is surprising in magnitude and the longer you are in it, the stranger your communication module reacts. It fluctuates between being as clear as glass to being completely incomprehensible. However, the severely impaired time periods are becoming longer. <b>You are only allowed to communicate from the third trick on. </b>'),
            		20 : _('Finally, the dust cloud clears and the wild swings of the communication module have noticeably reduced. Before you appears Jupiter in all its splendor. The gas giant is clearly appropriately named. Your absolute awe is interrupted when you notice the two damaged radar sensors. <b>Your commander determines who receives the tasks and carries out the repair, the commander cannot choose himself or herself.</b>'),
            		21 : _('After making the repairs you ascertain that you were clearly too close to Jupiter while you were passing through the cloud. Its gravitational force of around two and a half times that of Earth has drastically affected your course. In order to counteract the effect, you will have concentrate and work in a precise manner to reach the ideal exit point. During this, you hardly notice the <b>faulty radio communication.</b>'),
            		22 : _('You’re just barely out of the woods, when the temperature drops suddenly. All of the control systems alarms go off and you have to immediately start putting on your suits. The control module is struggling to keep up with the adjustments needed. Bypass the energy supply of the other modules successively in order to avoid a total blackout of the system. '),
            		23 : _('Most of the modules are still on emergency back-up supply while you puzzle over the cause of the rapid cooling. Callisto, one of the 79 moons of Jupiter, passes by, the moment you appear to escape the frost field. <b>Before you select the task cards, you can swap the position of two task tokens. Decide together but do not reveal anything about your own cards.</b>'),
            		24 : _('The incident has made a mess of your normal processes. There is a lot to do at the moment and none of you know where to start. Your commander takes the initiative and devises a plan in order to proceed in a organized manner. <b>Once everyone has looked at their cards, the commander asks each crew member about their willingness to take on the task. This may only be answered “yes” or “no.” The commander distributes the tasks.<br/>6 tasks</b>'),
            		25 : _('You’ve reached Saturn and pause to admire the magnificent spectacle of the rocky rings that constantly revolve around it. It is not without reason that they call it the ringed planet. Almost apathetically, you focus solely on the on-board analysis and collapse in exhaustion. Because of the dead zone, you won’t be disturbed. <b>If you are playing with five, you can now use the additional rule for five crew members.</b>'),
            		26 : _('A loud bang interrupts your introspective mood. Two space rocks that were in the vicinity of Saturn have torn holes in your ship’s hull. The on-board analysis module has immediately sealed off the affected storage area. Both rocks are still stuck in the shell of the hull. You must carefully remove them without further increasing the damage done. <b>At least two color cards, each with a value of one, must win one trick each.</b>'),
            		27 : _('You determine that the tear in the hull was not without consequences. A review of the modules associated with the area indicates damage to the flux capacitor. Although currently this is not a major problem, repairs will be necessary in the long-run, if you want to make it home. <b>Your commander specifies who should carry out the repair.</b>'),
            		28 : _('Readings are indicating an unusually high magnitude of cosmic rays in the area. You continue your flight oblivious, uninterrupted, and unaware that your radio messages are either not transmitting, or arriving with a great deal of time delay. This is not going to make working easy. <b>You are only allowed to communicate from the third trick on.</b> '),
            		29 : _('Your communication module appears to have suffered more damage than you initially thought. The repair requires a series of tests and calibrations that you must carry out together and with precision. <b>At no time may a crew member have won two tricks more than another crew member. Communication is down. </b>'),
            		30 : _('One part is done, but you postpone the rest of the repairs, as you are heading straight for Uranus. Its smooth, pale blue surface makes it look almost artificial. You tear yourself away from this fascinating view, because there are still repairs to be made. <b>You are only allowed to communicate from the second trick on.</b>'),
            		31 : _('As you slowly move away from Uranus, you receive a message from Earth requesting the collection of metadata from the moons of Uranus. Unfortunately, due to the radio interference, it arrived too late, you can only see three of the 27 moons — Rosalind, Belinda, and Puck. That will have to suffice for the time being. '),
            		32 : _('In the meantime, despite the favorable conditions, it is obvious how long you have been on the road together and the inevitable human characteristics begin to come to light. In order to avoid a heated conflict, everyone delves into his or her work. <b>Your commander takes over the organization and distributes the individual tasks.<br/>7 tasks</b> '),
            		33 : _('A space walk is imminent! One of the hatches is broken and needs to be repaired. But leaving the ship always poses a risk. <b>After everyone has looked at his or her cards, your commander asks about his or her willingness to volunteer. It may only be answered with a “yes” or “no.” Your commander then selects another crew member. The chosen crew member must win exactly one trick, but not with a rocket card. </b>'),
            		34 : _('Neptune is already in sight as your ship begins to waver. Man the stabilizers so as not to lose control. In the meantime your commander must realign the gravitation module. <b>At no time may a crew member have won two tricks more than another crew member. In addition, your commander must win the first and last trick. </b>'),
            		35 : _('Cautiously you reach the outermost planet of our solar system: the ice giant Neptune. Its deep blue makes you shiver. As you slowly pass Neptune, you receive another message from Earth. The space probe Alpha 5 is orbiting Neptune, but has damaged sensors. Find it and fix them. '),
            		36 : _('You take advantage of one of the rare moments of calm to socialize with each other. With all the minor and more major emergencies, the responsibility on your shoulders, and the uncertainty about the outcome of your adventure, tension has built up in every single crew member. So it’s good to just sit together, and talk. Relieved you re-dedicate yourselves to the coming challenges. <b>Your commander distributes the individual tasks. <br/>7 tasks</b>'),
            		37 : _('You reach the dwarf planet Pluto. Many years ago it would have been the ninth planet. You take a moment to reminisce about how your very educated mother used to serve you noodles, and talk to you about the planets while you reflect on the changeability of things. Nevertheless, the ship must be kept on course. <b>The commander decides who should take care of it.</b>'),
            		38 : _('You reach the heliopause, the border area of our solar system. If the calculations prove correct, the ninth planet cannot be far away. A feeling of excitement spreads, the hour of truth approaches. As your instruments react, you almost jump out of your seats. But unfortunately it is just a false alarm. <b>You are only allowed to communicate from the third trick on. </b>'),
            		39 : _('This has to be it! The readings indicated on your modules can only be produced by a truly gigantic object. The effects are so massive that even your <b>radio communication is interrupted</b>. Recalibrate your instruments and find out what’s really going on. '),
            		40 : _('You are now paying very close attention to the object, but are still uncertain what it might be. What appears in front of you could be a moon of Pluto. No wait, that’s no moon! You have found it! Planet Nine! Euphoria spreads among you and all the hardships are quickly forgotten. A surface scan of the planet suggests a solid crust. That would mean this is not another gas giant, it’s traversable. A fantastic opportunity is opening up. <b>Before you begin to distribute the task cards, you may move a task token to another task card that currently has no task tokens. Decide together but do not reveal anything about your own cards. </b>'),
            		41 : _('You regulate the engines to prepare for landing. Due to the completely new conditions, one of you has to take over the coordination of the landing. <b>After everyone has looked at his or her hand, your commander asks everyone about his or her readiness. It may only be answered with a “yes” or “no.” Your commander then selects another crew member for the task. This crew member’s task is to only win the first and last tricks. Since only the thrusters are used for correcting the position, neither trick may be won with rocket cards.</b>'),
            		42 : _('The planet is extremely cold and inhospitable but seems to be habitable. During your investigation you notice an area that appears to be moving away from your instruments. The closer you get to this anomaly, the more blatant the measurement errors become. What this means defies scientific explanation. At least you can narrow in on the phenomenon, because the results normalize when you move away.'),
            		43 : _('In the name of science, you venture closer. The gravitational laws seem to invert the closer you get to the anomaly and you need to anchor yourself to the planet’s surface using vibranium hooks for safety. <b>Your commander secures the rest of the crew and distributes the individual tasks.</b> The data you are collecting allows for only one conclusion: You have discovered a wormhole.<b><br/>9 tasks</b> '),
            		44 : _('Up until now, wormholes have been at most theoretical constructs and now here you are, standing right in front of one. It overshadows you like a black monolith — incomprehensible, but with a huge attraction. You send a message to Earth and prepare the engines for the jump. <b>Every rocket card must win a trick. First the one rocket card, then the two, the three, and finally the four.</b>'),
            		45 : _('The effect is monumental! You are strapped into your seats but feel like you’re everywhere at the same time. Colors and shapes change, and light feels like a swirling mass that behaves like an intelligent being and ensnares you. You focus on your instruments and try not to lose your mind. '),
            		46 : _('While you are being assailed with an overwhelming amount of information, you find you are still able to instinctively react to danger. Suddenly the main modules of the ship shut down during the jump, and red warning lights and alarms tear you from of your previously trance-like state. <b>Your task is that the crew member to the left of the member with the pink nine must win all pink cards. Say who has pink nine.</b>'),
            		47 : _('You are exhausted, at the end of your rope. The jump feels like a jail now, in which you can no longer distinguish between reality and fantasy. Your body screams that you can barely stand even ten more seconds of this, but your mind questions how long ten seconds actually is. You begin to count them down — and suddenly burst out of the wormhole.'),
            		48 : _('You barely have time to orient yourself. It is suddenly extremely hot everywhere. The on-board analysis module instantly shifts the entire ship into the highest danger level. The first of the modules begin to fall victim to the radical temperature fluctuations. Even in your regulated suits, you break into a sweat within seconds. You need to activate the emergency protocol, extend the heat shields, and get away from the heat source as quickly as possible. <b>The Omega task must be won in the last trick. </b>'),
            		49 : _('When you finally come to your senses, the situation has almost normalized. You just barely managed to take the necessary steps before you collapsed from exhaustion. Determining your current location you can hardly believe it. You are orbiting Venus! The wormhole is a direct link between Planet Nine and Venus, the second planet. This also explains the extreme heat, because Venus is significantly closer to the Sun than Earth. It takes a moment for the realization to dawn on you: You can go home! Check all ten main modules, focusing on life support, engines, and communication. Set course for Earth.'),
            		50 : _('The way back is much more complicated than expected. Some modules have been permanently damaged during the trip and you will have to fight against the immense gravitational pull of the Sun. With your reserves depleted, you cannot allow yourself any mistakes — the voyage home must be executed precisely. You must first take advantage of a gravitational catapult. Then, the ship’s modules must be kept under control and the approach to Earth initiated. In the end, landing on Earth will require no less attention than any other maneuver you’ve performed so far. <b>Everyone looks at his or her cards. A crew member must win the first four tricks. Another crew member has to win the last trick. The remaining crew members must win all the tricks in between. Your commander asks everyone for his or her preferred task, you then decide together as a crew who should assume which role. Do not say anything about your cards. </b>'),

            };
        },

        /*
            setup:

            This method must set up the game user interface according to current game situation specified
            in parameters.

            The method is called each time the game interface is displayed to a player, ie:
            _ when the game starts
            _ when a player refreshes the game page (F5)

            "gamedatas" argument contains all datas retrieved by your "getAllDatas" PHP method.
        */

        setup: function( gamedatas )
        {

            this.players = gamedatas.players;
            this.colors = gamedatas.colors;

            // Number of turns
            this.mission_counter = new ebg.counter();
            this.mission_counter.create('mission_counter');
            this.mission_counter.setValue(gamedatas.mission);
            this.attempts_counter = new ebg.counter();
            this.attempts_counter.create('try_counter');
            this.attempts_counter.setValue(gamedatas.mission_attempts);
            this.total_attempts_counter = new ebg.counter();
            this.total_attempts_counter.create('total_try_counter');
            this.total_attempts_counter.setValue(gamedatas.total_attempts);
            $('mission_description').innerHTML = _(this.missions[gamedatas.mission]);

            var playernb = 0;
            var ordered = [];
            for( var player_id in gamedatas.players )
            {
            	playernb++;
            	ordered[gamedatas.players[player_id]['relative']] = gamedatas.players[player_id];
            }


            // Setting up player boards
            for( var rel in ordered )
            {
                var player = ordered[rel];
                var player_id = player['id'];
                var row = 3;
                var relative = player['relative'];
                if(relative == 1 || relative == 2 || (playernb == 5 && relative == 3))
	            {
	                	row = 1;
                }

                dojo.place( this.format_block('jstpl_player_board', player), 'row'+row);
                dojo.place( this.format_block('jstpl_player_table_card', player), 'trow'+row);
                dojo.place( this.format_block('jstpl_player_check_ok', player), 'erow'+row);

                var cardontable =  player['cardontable'];
                if(cardontable  !== undefined)
                {
                	dojo.place( this.format_block('jstpl_cardontable', cardontable), $('playertablecard_'+this.players[player_id]['relative']));
                    this.createCardTooltip('card_' + cardontable['id'], cardontable['type'], cardontable['type_arg']);
                }

                var cardontable =  player['comm'];
                if(cardontable  !== undefined)
                {
                	dojo.place( this.format_block('jstpl_cardontable', cardontable), $('comcard_'+player_id));
                	dojo.query('#card_' + cardontable['id']).connect('onclick', this, 'onPlayCard');
                    this.createCardTooltip('card_' + cardontable['id'], cardontable['type'], cardontable['type_arg']);
                }

                dojo.place(this.format_block('jstpl_player', {player_id:player_id}), 'player_board_' + player_id);

                this.trick_counters[player_id] = new ebg.counter();
                this.trick_counters[player_id].create('trick_counter_' + player_id);
                this.trick_counters[player_id].setValue(player['player_trick_number']);

                this.cards_counters[player_id] = new ebg.counter();
                this.cards_counters[player_id].create('cardsinhands_counter_' + player_id);
                this.cards_counters[player_id].setValue(player['cards_number']);

                this.addCustomTooltip('tricks_' + player_id, _('Number of tricks won.'))
                this.addCustomTooltip('cardsinhand_' + player_id, _('Number of cards in hand.'))

                var commander_desc = _('The commander is always the player with the four rocket. <br/>The duties of the commander are: <br/>1. start the selection of the tasks <br/>2. start the first trick <br/>3. implement the special rules of individual missions');
                this.addTooltipHtml( 'commander_in_panel_' + player_id, this.format_block('jstpl_tooltip_common', {title: _('Commander token'), description:  commander_desc}));
                this.addTooltipHtml( 'commander_icon_spot_' + player_id, this.format_block('jstpl_tooltip_common', {title: _('Commander token'), description: commander_desc }));

                this.addTooltipHtml( 'special_icon_spot_' + player_id, this.format_block('jstpl_tooltip_common', {title: _('Special one'), description: _('This crew member is special for this mission.') }));
                this.addTooltipHtml( 'special2_icon_spot_' + player_id, this.format_block('jstpl_tooltip_common', {title: _('Second special one'), description: _('This crew member must win the last trick.') }));

                dojo.removeClass('radio_' + player['id']);
                dojo.addClass('radio_' + player['id'], 'radio');
                dojo.addClass('radio_' + player['id'], player['comm_token']);

                this.addTooltipHtml( 'radio_' + player_id, this.format_block('jstpl_tooltip_common', {title: _('Radio communication token'), description:  _('Communication token gives information of the communicated color card :<br/>- At the top, if it is your highest card of this color.<br/>- In the middle, if it is your only card of this color.<br/>- At the bottom, if it is your lowest card of this color.<br/>- Red, you cannot communicate.')}));

            }

            this.player_hand = new ebg.stock();
            this.player_hand.create(this, $('myhand'), this.card_width, this.card_height);
            this.player_hand.image_items_per_row = 11;
            this.player_hand.setSelectionMode(0);
            this.player_hand.setSelectionAppearance('class');
            this.player_hand.centerItems = true;
            this.player_hand.onItemCreate = dojo.hitch(this, 'onCreateNewCard');



            var position = 0;
            for(var color=1;color<=5;color++) {
                for(var value=1;value<=(color==5 ? 4 : 9);value++) {
                    // Build card type id
                    var card_id = this.getCardUniqueId(color, value);

                    var position_in_sprite = (color-1)*11+value-1;
                    if(color==5)
                    {
                    	position_in_sprite = 9 + (value-1)*11;
                    }
                    this.player_hand.addItemType(card_id, position, g_gamethemeurl + 'img/cards.png', position_in_sprite);
                    position++;
                }
            }

            var card_id = this.getCardUniqueId(6, 0);
            this.player_hand.addItemType(card_id, position, g_gamethemeurl + 'img/cards.png', 21);
            position++;


    		dojo.query(".commander").addClass("hidden");
    		dojo.query("#commander_icon_spot_"+gamedatas.commander_id).removeClass("hidden");
    		dojo.query(".commander_in_panel").addClass("hidden");
    		dojo.query("#commander_in_panel_"+gamedatas.commander_id).removeClass("hidden");
    		dojo.query(".special").addClass("hidden");
    		dojo.query("#special_icon_spot_"+gamedatas.special_id).removeClass("hidden");
    		dojo.query(".special2").addClass("hidden");
    		dojo.query("#special2_icon_spot_"+gamedatas.special2_id).removeClass("hidden");


         // Cards in player's hand
            for(var i in this.gamedatas.hand) {
                var card = this.gamedatas.hand[i];
                var color = card.type;
                var value = card.type_arg;
                this.player_hand.addToStockWithId(this.getCardUniqueId(color, value), card.id);
    			dojo.query("#myhand_item_"+card.id).connect('onclick', this, 'onPlayCard');
            }

            for( var task_id in this.gamedatas.tasks )
            {
                var task = this.gamedatas.tasks[task_id];
            	dojo.place( this.format_block('jstpl_task', task), $('tasks_'+task['player_id']));
                this.createTaskTooltip(task);
            }
			dojo.query(".card_com").connect('onclick', this, 'onStartComm');
			dojo.query(".card_com .cardontable").connect('onclick', this, 'onStartComm');
            dojo.query('.finalbutton' ).connect( 'onclick', this, 'onButtonChoose');

            if(this.gamedatas.distress == 1)
            {
            	dojo.query("#distress").addClass("activated");
            }
            this.addTooltipHtml( 'distress', this.format_block('jstpl_tooltip_common', {title: _('Distress token'), description:  _('A distress signal can be sent out before the first trick of a mission and before any communication. If the distress signal is activated, each crew member may pass one card to his neighbor. Rockets may not be passed on!')}));

            dojo.query('#distress' ).connect( 'onclick', this, 'onDistress');
            dojo.query('.playertable' ).connect( 'onclick', this, 'onPickCrew');

            if(gamedatas.players[this.player_id] != undefined)
            {
	            if(gamedatas.players[this.player_id]['comm_pending'] == 1)
	            {
	            	dojo.addClass('comcard_'+this.player_id, 'commpending');
	            }
	            if(gamedatas.players[this.player_id]['canCommunicate'] == 1)
	            {
	            	dojo.addClass('comcard_'+this.player_id, 'selectablecomm');
	            }
            }

            if(this.gamedatas.show_intro)
            {
            	this.startCampaign();
            }

            // Setup game notifications to handle (see "setupNotifications" method below)
            this.setupNotifications();

        },


        ///////////////////////////////////////////////////
        //// Game & client states

        // onEnteringState: this method is called each time we are entering into a new game state.
        //                  You can use this method to perform some user interface changes at this moment.
        //
        onEnteringState: function( stateName, args )
        {
        	this.stateName = stateName;
    		dojo.query("#playertable_central").removeClass("hidden");
    		dojo.query("#tasks").addClass("hidden");
    		dojo.query("#endPanel").addClass("hidden");

            switch( stateName )
            {

            case 'pickTask':
        		this.showTasks(args.args.tasks);
        		if(this.isCurrentPlayerActive())
        		{
        			dojo.query("#tasklists .taskontable").addClass("selectable");
        			dojo.query("#tasklists .taskontable").connect('onclick', this, 'onChooseTask');
        		}

            	break;

            case 'playerTurn':
        		if(this.isCurrentPlayerActive())
        		{
	            	for( var card_id in args.args.cards )
	                {
	        			dojo.query("#myhand_item_"+args.args.cards[card_id]).addClass("selectable");
	        			dojo.query("#card_"+args.args.cards[card_id]).addClass("selectable");
	                }
        		}

        		if(args.args.canDistress)
        		{
        			dojo.query("#distress").addClass("selectable");
        		}
        		else
        		{
        			dojo.query("#distress").removeClass("selectable");
        		}
            	break;
            case 'comm':
    			dojo.query(".selectable").removeClass("selectable");
            	if(this.isCurrentPlayerActive())
        		{
	            	for( var card_id in args.args )
	                {
	        			dojo.query("#myhand_item_"+card_id).addClass("selectable");
	                }
        		}
            	break;

            case 'commToken':
            	if(this.isCurrentPlayerActive())
        		{
            		for( var status_id in args.args.possible )
	                {
                        var status = args.args.possible[status_id];
                        debugger;
                    	dojo.place( this.format_block('jstpl_temp_comm', {player_id: this.player_id , status:status}), $('comcard_'+this.player_id), 'first');
	                }

        			dojo.query(".radio_temp").connect('onclick', this, 'onFinishComm');
        			this.addTooltipHtmlToClass( "radio_temp", this.format_block('jstpl_tooltip_common', {title: _('Radio communication token'), description:  _('Communication token gives information of the communicated color card :<br/>- At the top, if it is your highest card of this color.<br/>- In the middle, if it is your only card of this color.<br/>- At the bottom, if it is your lowest card of this color.<br/>- Red, you cannot communicate.')}));

        		}
            	break;

            case 'endMission':
        		dojo.query("#playertable_central").addClass("hidden");
        		dojo.query("#endPanel").removeClass("hidden");
        		if(args.args.end>0)
        		{
        			$('endResult').innerHTML = _('Mission ${nb} <span class="success">completed</span>').replace('${nb}',args.args.number);
        		}
        		else
        		{
        			$('endResult').innerHTML = _('Mission ${nb} <span class="failure">failed</span>').replace('${nb}',args.args.number);
        		}
        		if(!this.isCurrentPlayerActive())
        		{
        			dojo.query(".finalbutton").addClass("hidden");
        		}
        		else
        		{
        			dojo.query(".finalbutton").removeClass("hidden");
        		}

    			dojo.query("#endPanel .check_ok").addClass("check_confirm");
        		for(var player_id in this.getActivePlayers())
        		{
        			var pid = this.getActivePlayers()[player_id];
        			dojo.query("#continue_ok_"+pid).removeClass("check_confirm");
        		}

            	break;


            case 'distress':

            	if(this.isCurrentPlayerActive())
        		{
	            	for( var card_id in args.args.cards )
	                {
	        			dojo.query("#myhand_item_"+card_id).addClass("selectable");
	                }
        		}
                break;

            case 'question':
        		this.showTasks(args.args.tasks);
            	break;

            case 'pickCrew':
        		this.showTasks(args.args.tasks);
            	if(this.isCurrentPlayerActive())
        		{
            		for( var player_id in args.args.possible )
                    {
            			dojo.query("#playertable_"+player_id).addClass("selectable");
                    }
        		}
            	break;

            case 'multiSelect':
        		this.showTasks(args.args.tasks);
        		this.multiSelect = args.args.ids;
                this.selected = null;

                dojo.query('.task_marker' ).connect( 'onclick', this, 'onMultiSelect');
    			dojo.query("#tasklists .taskontable").connect('onclick', this, 'onMultiSelect');

        		if(this.isCurrentPlayerActive())
        		{
            		for( var id in args.args.ids )
                    {
        				dojo.query("#"+id).addClass("selectable");
                    }
        		}
            	break;

            case 'dummmy':
                break;
            }
        },

        showTasks: function(tasks)
        {
        	dojo.query("#playertable_central").addClass("hidden");
    		dojo.query("#tasks").removeClass("hidden");
    		dojo.empty("tasklists");
    		for( var task_id in tasks )
            {
                var task = tasks[task_id];
            	dojo.place( this.format_block('jstpl_task', task), $('tasklists'));
                this.createTaskTooltip(task);
            }
        },

        // onLeavingState: this method is called each time we are leaving a game state.
        //                 You can use this method to perform some user interface changes at this moment.
        //
        onLeavingState: function( stateName )
        {

            switch( stateName )
            {

            case 'commToken':
                debugger;
            	dojo.query(".radio_temp").forEach(dojo.destroy);
                break;
            case 'pickCrew':
            	dojo.query(".discussion_bubble").addClass("hidden");
            	break;
            case 'playerTurn':
    			dojo.query("#distress").removeClass("selectable");
            	dojo.query(".discussion_bubble").addClass("hidden");
                break;

            case 'dummmy':
                break;
            }
        },

        // onUpdateActionButtons: in this method you can manage "action buttons" that are displayed in the
        //                        action status bar (ie: the HTML links in the status bar).
        //
        onUpdateActionButtons: function( stateName, args )
        {

            if( this.isCurrentPlayerActive() )
            {
                switch( stateName )
                {
	            case "multiSelect":
            		this.addActionButton('cancel_button', _("No move") , 'onCancel');
            		break;

	            case "comm":
            		this.addActionButton('cancel_button', _("Cancel") , 'onCancel');
            		break;

		            case "question":
		            	var i = 0;
		            	var split = args.replies.split("/");
		            	for (i = 0; i < split.length; i++)
		            	{
			            	this.addActionButton(i+'_button', split[i] , 'onButtonChoose');
		            	}
		            	break;

		            case "distressSetup":
		            	this.addActionButton('left_button', _("Left") , 'onButtonChoose');
		            	this.addActionButton('right_button', _("Right") , 'onButtonChoose');
		            	this.addActionButton('no_button', _("No card passed") , 'onButtonChoose');
		            	break;
                }
            }
        },

        onButtonChoose:function(event)
        {
            dojo.stopEvent( event );
            if(this.isCurrentPlayerActive()&& this.checkAction( "actButton" ))
            {
            	$action = event.currentTarget.id.replace('_button','');

    			dojo.query(".finalbutton").addClass("hidden");

            	this.ajaxcall('/thecrew/thecrew/actButton.html', {
                    lock:true,
                    choice:$action,
                },this, function( result ) {
                }, function( is_error ) { } );
            }
       },



       onDistress: function (event) {
    	   dojo.stopEvent( event );

	       	if(event.currentTarget.classList.contains('selectable') ) {


	            this.confirmationDialog( _("Are you sure you want to launch a distress signal?"), dojo.hitch( this, function() {

	       		dojo.query(".selectable").removeClass("selectable");

	       		this.ajaxcall('/thecrew/thecrew/actDistress.html', {
		                   lock:true,
		                },this, function( result ) {
		                }, function( is_error ) { } );
	            } ) );
                return;
	       	}

       },

        onCancel: function (event) {
            dojo.stopEvent( event );
            if( this.checkAction( "actCancel" ) ) {
        		dojo.query(".selectable").removeClass("selectable");
                this.ajaxcall('/thecrew/thecrew/actCancel.html', {
                    lock:true,
                },this, function( result ) {}, function( is_error ) { } );
            }
        },

        onStartComm:function(event)
        {
            dojo.stopEvent( event );
        	if((event.currentTarget.classList.contains('selectablecomm') || event.currentTarget.parentNode.classList.contains('selectablecomm')) && ((this.stateName != "comm" && this.stateName != "commToken") || !this.isCurrentPlayerActive() ) ) {

        		dojo.query(".selectablecomm").removeClass("selectablecomm");

        		this.ajaxcall('/thecrew/thecrew/actStartComm.html', {
 	                   lock:true,
 	                },this, function( result ) {
 	                }, function( is_error ) { } );
        	}

        },

        onFinishComm:function(event)
        {
            dojo.stopEvent( event );

        	if(event.currentTarget.classList.contains('selectable') ) {

        		dojo.query(".selectable").removeClass("selectable");

        		var place = event.currentTarget.classList[event.currentTarget.classList.length-1];

        		this.ajaxcall('/thecrew/thecrew/actFinishComm.html', {
 	                   lock:true,
 	                   place:place
 	                },this, function( result ) {
 	                }, function( is_error ) { } );
        	}

        },

        onPickCrew:function(event)
        {
        	dojo.stopEvent( event );
            if(this.isCurrentPlayerActive())
            {
            	if(event.currentTarget.classList.contains('selectable') && this.checkAction( "actPickCrew" ) ) {

            		var split = event.currentTarget.id.split('_');
	            		var id = split[split.length - 1];

	                    dojo.query(".selectable").removeClass("selectable");

	            		this.ajaxcall('/thecrew/thecrew/actPickCrew.html', {
		 	                   lock:true,
		 	                  crewId:id
		 	                },this, function( result ) {
		 	                }, function( is_error ) { } );
            	}
            }
        },

        onMultiSelect:function(event)
        {
        	dojo.stopEvent( event );
            if(this.isCurrentPlayerActive() && this.stateName == "multiSelect" )
            {
            	if(event.currentTarget.classList.contains('selected'))
            	{
            		this.selected = null;
                    dojo.query(".selected").removeClass("selected");
                    for( var id in this.multiSelect )
                    {
        				dojo.query("#"+id).addClass("selectable");
                    }
            	}
            	else if(event.currentTarget.classList.contains('selectable') && this.checkAction( "actMultiSelect" ) ) {

            			var id = event.currentTarget.id
	                    dojo.query(".selectable").removeClass("selectable");

	                    if(this.selected == null)
	                    {
	                    	this.selected = id;
		                    dojo.query("#"+id).addClass("selected");

		                    for( var id in this.multiSelect[this.selected] )
		                    {
		        				dojo.query("#"+id).addClass("selectable");
		                    }
	                    }
	                    else
	                    {
		                    dojo.query(".selected").removeClass("selected");
		                    dojo.query(".selectable").removeClass("selectable");
		            		this.ajaxcall('/thecrew/thecrew/actMultiSelect.html', {
			 	                   lock:true,
				 	                  id1:this.selected,
				 	                  id2:id,
			 	                },this, function( result ) {
			 	                }, function( is_error ) { } );

	                    }
            	}
            }
        },


        onPlayCard:function(event)
        {
            dojo.stopEvent( event );
            if(this.isCurrentPlayerActive())
            {
            	if(event.currentTarget.classList.contains('selectable') && this.checkAction( "actPlayCard" ) ) {
            		var split = event.currentTarget.id.split('_');
	            		var id = split[split.length - 1];

	                    dojo.query(".selectable").removeClass("selectable");

	            		this.ajaxcall('/thecrew/thecrew/actPlayCard.html', {
		 	                   lock:true,
		 	                  cardId:id
		 	                },this, function( result ) {
		 	                }, function( is_error ) { } );
            	}
            }
        },

        onChooseTask:function(event)
        {
            dojo.stopEvent( event );
            if(this.isCurrentPlayerActive())
            {
            	if(event.currentTarget.classList.contains('selectable')  && this.checkAction( "actChooseTask" ) ) {

            		var id = event.currentTarget.id.split('_')[1];

                    dojo.query(".selectable").removeClass("selectable");

            		this.ajaxcall('/thecrew/thecrew/actChooseTask.html', {
	 	                   lock:true,
	 	                  taskId:id
	 	                },this, function( result ) {
	 	                }, function( is_error ) { } );
            	}
            }
        },


        // Get card unique identifier based on its color and value
        getCardUniqueId: function(color, value) {
            this.last_color = color;
            this.last_value = value;
            return (color-1)*10+(value-1);
        },

     // Enable to declare an optional parameter and its value
        setDefault: function(variable, default_value) {
            return variable === undefined ? default_value : variable;
        },

        // Gets the image of the symbol from the sprite
        symbol: function(name) {
            return dojo.string.substitute("<span class='logicon ${name}'></span>", {'name' : name});
        },

        /*
         * Tooltip management
         */
        shapeTooltip: function(help_HTML, action_HTML) {
            var help_string_passed = help_HTML != "";
            var action_string_passed = action_HTML != "";
            var HTML = "<table class='tooltip'>${content}</table>";
            if (help_string_passed) {
                help_string = this.format_string_recursive("<tr><td>${text}</td></tr>", { 'text': help_HTML});
            }
            if (action_string_passed) {
                action_string = this.format_string_recursive("<tr><td>${text}</td></tr>", {'text': action_HTML});
            }

            if (help_string_passed && action_string_passed) {
                content = this.format_string_recursive('${help}${action}', {'help': help_string, 'action': action_string});
                HTML = this.format_string_recursive(HTML, {'content': content});
            }
            else if (help_string_passed) {
                HTML = this.format_string_recursive(HTML, {'content': help_string});
            }
            else { // action_string_passed
                HTML = this.format_string_recursive(HTML, {'content': action_string});
            }
            return HTML;
        },

        addCustomTooltip: function(nodeId, help_HTML, action_HTML, delay) {
            // Default values
            action_HTML = this.setDefault(action_HTML, '');
            delay = this.setDefault(delay, undefined);
            ///////
            this.addTooltipHtml(nodeId, this.shapeTooltip(help_HTML, action_HTML), delay);
        },

        addCustomTooltipToClass: function(cssClass, help_HTML, action_HTML, delay) {
            // Default values
            action_HTML = this.setDefault(action_HTML, '');
            delay = this.setDefault(delay, undefined);
            ///////
            this.addTooltipHtmlToClass(cssClass, this.shapeTooltip(help_HTML, action_HTML), delay);
        },

        onCreateNewCard: function(card_div, card_type_id, card_HTML_id) {
            this.createCardTooltip(card_HTML_id, this.last_color, this.last_value)
        },

        createCardTooltip : function(card_HTML_id, color, value) {
            if(color == 6)
            {
            	msg = _("Reminder card");
                 this.addTooltipHtml( card_HTML_id, this.format_block('jstpl_tooltip_common', {title: _('Reminder card'), description: _('On table, it allows you to start communication by clicking on it <b>before</b> a trick. Once requested, communication card will be highlighted in green. <br/>In hand, its purpose is to remind you that your communicated card is still on the table.') }));

            }
            else
            {
            	var msg = this.format_string_recursive(_("${value_symbol}${color_symbol} : ${value_name} ${color_name}."), {'value_name' :  value, 'color_name' : color, 'value_symbol' : value, 'color_symbol' : color});
            	this.addCustomTooltip(card_HTML_id, dojo.string.substitute('<span class="card_description">${msg}</span>', {'msg':msg}));
            }
         },
        createTaskTooltip : function(task) {
        	var card_HTML_id = 'task_' + task['task_id'];
        	var color = task['card_type'];
        	var value = task['card_type_arg'];
            var token = task['token'];
        	var msg = this.format_string_recursive(_("<b>Task</b><br/> ${value_symbol}${color_symbol} : ${value_name} ${color_name}."), {'value_name' :  value, 'color_name' : color, 'value_symbol' : value, 'color_symbol' : color});
            if(token !=null && this.task_markers[token] != undefined)
            {
            	msg = msg + '<br/>'+this.task_markers[token];
            }
            if(color == 7)
            {
            	msg = _('<b>Task</b>');
            }

            this.addCustomTooltip(card_HTML_id, dojo.string.substitute('<span class="card_description">${msg}</span>', {'msg':msg}));
        },

        ///////////////////////////////////////////////////
        //// Player's action

        /*

            Here, you are defining methods to handle player's action (ex: results of mouse click on
            game objects).

            Most of the time, these methods:
            _ check the action is possible at this game state.
            _ make a call to the game server

        */




        ///////////////////////////////////////////////////
        //// Reaction to cometD notifications

        /*
            setupNotifications:

            In this method, you associate each of your game notifications with your local method to handle it.

            Note: game notification names correspond to "notifyAllPlayers" and "notifyPlayer" calls in
                  your thecrew.game.php file.

        */
        setupNotifications: function()
        {
            dojo.subscribe('cleanUp', this, "notif_cleanUp");
            dojo.subscribe('commander', this, "notif_commander");
            dojo.subscribe('special', this, "notif_special");
            dojo.subscribe('newHand', this, "notif_newHand");
            dojo.subscribe('takeTask', this, "notif_takeTask");
            dojo.subscribe('playCard', this, "notif_playCard");
            dojo.subscribe('commCard', this, "notif_commCard");
            dojo.subscribe('continue', this, "notif_continue");
            dojo.subscribe('move', this, "notif_move");
            dojo.subscribe('resetComm', this, "notif_resetComm");
            dojo.subscribe('commpending', this, "notif_commpending");
            dojo.subscribe('distress', this, "notif_distress");
            dojo.subscribe('speak', this, "notif_speak");
            dojo.subscribe('give', this, "notif_give");
            dojo.subscribe('receive', this, "notif_receive");
            dojo.subscribe('taskUpdate', this, "notif_taskUpdate");
            dojo.subscribe('endComm', this, "notif_endComm");
            dojo.subscribe('trickWin', this, "notif_trickWin");
            dojo.subscribe('noPremium', this, "notif_nopremium");
            dojo.subscribe('giveAllCardsToPlayer', this, "notif_giveAllCardsToPlayer");

            this.notifqueue.setSynchronous('move', 1000);
            this.notifqueue.setSynchronous('trickWin', 1000); // Reasonable time for players to see the cards played before they are gathered
            this.notifqueue.setSynchronous('giveAllCardsToPlayer', 2000); // The time needed for cards to move and disappear

        },

        notif_giveAllCardsToPlayer: function(notif) {
            // Move all cards on table to given table, then destroy them
            var winner_id = notif.args.player_id;
            this.trick_counters[winner_id].incValue(1); // Increase the trick counter by one
            for(var card_id in notif.args.cards) {
            	var card = notif.args.cards[card_id];
                var b_winning_card = (winner_id == card['location_arg']);
                dojo.style('card_' + card['id'], 'z-index', 11+b_winning_card); // Ensure that the winning card stays on top

                var anim = this.slideToObject('card_' + card['id'], 'playertablecard_' + this.players[winner_id]['relative'], 1000); // Program the anim: 1. Slide the cart (1 second)
                if (b_winning_card) {
                    self = this;
                    dojo.connect(anim, 'onEnd', function(node) {self.fadeOutAndDestroy(node, 1000);}); // 2. Delete it (1 second for the top card)
                }
                else {
                    dojo.connect(anim, 'onEnd', function(node) {dojo.destroy(node);}); // 2. Delete it (immediately for card under the top card)
                }
                anim.play(); // Launch the anim (total 2 seconds => match the setSynchronous value)
            }
        },

        notif_nopremium : function(notif)
        {
        	this.myDlg = new ebg.popindialog();
        	this.myDlg.create( 'myDialogUniqueId' );
        	this.myDlg.setTitle( _("Premium member required") );
        	this.myDlg.setMaxWidth( 500 ); // Optional
        	var html = _('Congratulations for your success !<br/><br/>The Crew board game is including 50 different missions.<br/>You can access the 10 first missions for free, and you can access the other 40 missions if at least one Premium member is around the table.<br/><br/><a href="https://boardgamearena.com/premium">Go Premium now</a><br/><br/>Good luck for your Quest.');
        	this.myDlg.setContent( html );
        	this.myDlg.show();

        },

        startCampaign : function()
        {
        	this.myDlg = new ebg.popindialog();
        	this.myDlg.create( 'myDialogUniqueId' );
        	this.myDlg.setTitle( _("LOGBOOK") );
        	this.myDlg.setMaxWidth( 500 ); // Optional
        	var html = _('After years of discussion, the International Astronomical Union decided on August 24th, 2006, to withdraw Pluto’s status as the ninth planet in our solar system. From that day on, there were only eight planets in our solar system, Neptune being the eighth and the furthest away from the Sun.<br/><br/> Years later, however, a sensational theory emerged — that a huge, hitherto unknown heavenly body must be positioned at the edge of our solar system. The origin of these theories was the data transmitted by the spacecraft Voyager 2 and then later by New Horizons. Unusual distortions in their measurements and phased interruptions in their transmissions left scientists perplexed. Initially dismissed by their peers as a figment of their imagination, many skeptics eventually became convinced by the evidence over time. However, the data ultimately proved inconclusive. Even though a cadre of scientists had thoroughly examined it, it still had not provided any concrete evidence of the theory.<br/><br/> Out of options, the research team built around Dr. Markow created project NAUTILUS: A manned mission that would be sent to verify the existence of Planet Nine. After years of research and countless setbacks, they had finally developed the technology to carry out the mission. And now the real question is: with what crew? Are you ready to join project NAUTILUS? Volunteers needed!');
        	this.myDlg.setContent( html );
        	this.myDlg.show();

        },

        notif_speak: function(notif)
        {
        	var elem = dojo.byId("discussion_bubble_tasks_"+notif.args.player_id);
        	if(elem == null)
        	{
	        	this.showBubble( 'playertable_'+notif.args.player_id, notif.args.content, 0, 10000, 'bubble_custom' ) ;
        	}
        },


        notif_continue: function(notif)
        {
			dojo.query("#continue_ok_"+notif.args.player_id).addClass("check_confirm");
        },

        notif_taskUpdate: function(notif) {
            dojo.removeClass('status_' + notif.args.task['task_id'], 'tbd');
            dojo.addClass('status_' + notif.args.task['task_id'], notif.args.task['status']);
        },

        notif_endComm: function(notif) {
            dojo.removeClass('radio_' + notif.args.player_id);
            dojo.addClass('radio_' + notif.args.player_id, 'radio appears');
            dojo.addClass('radio_' + notif.args.player_id, notif.args.comm_status);
            this.addTooltipHtml( 'radio_' + notif.args.player_id, this.format_block('jstpl_tooltip_common', {title: _('Radio communication token'), description:  _('Communication token gives information of the communicated color card :<br/>- At the top, if it is your highest card of this color.<br/>- In the middle, if it is your only card of this color.<br/>- At the bottom, if it is your lowest card of this color.<br/>- Red, you cannot communicate.')}));

        },

        notif_commander: function(notif) {
        	dojo.query(".commander").addClass("hidden");
    		dojo.query("#commander_icon_spot_"+notif.args.player_id).removeClass("hidden");
    		dojo.query(".commander_in_panel").addClass("hidden");
    		dojo.query("#commander_in_panel_"+notif.args.player_id).removeClass("hidden");
        },

        notif_special: function(notif) {
        	if(notif.args.special2 == true)
        	{
	        	dojo.query(".special2").addClass("hidden");
	    		dojo.query("#special2_icon_spot_"+notif.args.player_id).removeClass("hidden");
        	}
        	else
        	{
	        	dojo.query(".special").addClass("hidden");
	    		dojo.query("#special_icon_spot_"+notif.args.player_id).removeClass("hidden");
        	}
        },

        notif_move: function(notif) {

            this.attachToNewParent( notif.args.item_id, notif.args.location_id);
            this.createTaskTooltip(notif.args.task);

            dojo.animateProperty({
	       	    node: notif.args.item_id,
	       	    duration: 1000,
	            easing: dojo.fx.easing.expoInOut,
	       	    properties: {
	               left: 13,
	               bottom: 4,
	       	    }

	       	  }).play();
        },

        notif_trickWin: function(notif) {
            // We do nothing here (just wait in order players can view the cards played before they are gathered
        },

        notif_playCard: function(notif) {
            // Play a card on the table
            this.playCardOnTable(notif.args.card);
            this.cards_counters[notif.args.card.location_arg].incValue(-1);
        },

        notif_resetComm: function(notif)  {

        	var player_id = notif.args.card['location_arg'];
            if (player_id == this.player_id) {
            	this.player_hand.removeFromStockById(notif.args.reminder_id);
            }
            dojo.removeClass('radio_' + notif.args.player_id);
            dojo.addClass('radio_' + notif.args.player_id, 'radio appears used');
            this.playCardOnTable(notif.args.card, true);
        },

        notif_commCard: function(notif) {

        	var player_id = notif.args.card['location_arg'];
            if (player_id == this.player_id) {
            	this.player_hand.addToStockWithId(this.getCardUniqueId(6, 0), notif.args.reminder_id);
            	dojo.query("#myhand_item_"+notif.args.reminder_id).connect('onclick', this, 'onPlayCard');
            }
        	dojo.addClass('radio_'+player_id, 'hidden');
        	dojo.destroy('card_'+notif.args.reminder_id);
            this.playCardOnTable(notif.args.card, true);
        },


        notif_commpending: function(notif) {

        	if(notif.args.pending == 1)
        	{
            	dojo.addClass('comcard_'+this.player_id, 'commpending');
        	}
        	else
        	{
            	dojo.removeClass('comcard_'+this.player_id, 'commpending');
        	}

    		if(notif.args.canCommunicate == 1)
    		{
    			dojo.query("#comcard_"+this.player_id).addClass("selectablecomm");
    		}
    		else
    		{
    			dojo.query("#comcard_"+this.player_id).removeClass("selectablecomm");
    		}

        },

        notif_takeTask: function(notif) {
            var task = notif.args.task;
            var taskId = 'task_'+task['task_id'];
            this.attachToNewParent( taskId, 'tasks_'+task['player_id'] );

            if($(taskId).classList.contains('col7'))
            {
            	//reveal task if necessary
           		dojo.query("#"+taskId).removeClass("col7 val0");
           		dojo.query("#"+taskId).addClass("col"+task['card_type']+" val"+task['card_type_arg']);
            }
            this.createTaskTooltip(task);

            dojo.animateProperty({
	       	    node: 'task_'+task['task_id'],
	       	    duration: 1000,
	            easing: dojo.fx.easing.expoInOut,
	       	    properties: {
	               left: 0,
	               top: 0,
	       	    }

	       	  }).play();

        },


        notif_distress: function(notif) {
       		dojo.query(".selectable").removeClass("selectable");
        	dojo.query("#distress").addClass("activated");
        	this.attempts_counter.incValue(1);
        	this.total_attempts_counter.incValue(1);
        },

        notif_give: function(notif) {
            this.player_hand.removeFromStockById(notif.args.card_id);
        },

        notif_receive: function(notif) {

            var card = notif.args.card;
            var color = card.type;
            var value = card.type_arg;
            this.player_hand.addToStockWithId(this.getCardUniqueId(color, value), card.id);
			dojo.query("#myhand_item_"+card.id).connect('onclick', this, 'onPlayCard');
        },

        notif_cleanUp: function(notif) {
            dojo.query('.taskontable').forEach(dojo.destroy);

            this.commander_id = null;
    		dojo.query(".commander_in_panel").addClass("hidden");
    		dojo.query(".commander").addClass("hidden");
        	dojo.query(".special").addClass("hidden");
        	dojo.query(".special2").addClass("hidden");

    	     this.mission_counter.setValue(notif.args.mission);
             this.attempts_counter.setValue(notif.args.mission_attempts);
             this.total_attempts_counter.setValue(notif.args.total_attempts);
             if(notif.args.distress == 1)
             {
             	dojo.query("#distress").addClass("activated");
             }
             else
             {
              	dojo.query("#distress").removeClass("activated");
             }

             $('mission_description').innerHTML = this.missions[notif.args.mission];
         	dojo.query(".cardontable").forEach(dojo.destroy);

    		for( var player_id in this.players )
            {
                this.trick_counters[player_id].setValue(0);
                this.cards_counters[player_id].setValue(notif.args.players[player_id]['nbCards']);

                this.playCardOnTable(notif.args.players[player_id]['comCard'], true);

                dojo.removeClass('radio_' + player_id);
                dojo.addClass('radio_' + player_id, 'radio appears middle');
            }
			dojo.query(".card_com .cardontable").connect('onclick', this, 'onStartComm');
        },

        
        notif_updatePlayerScore: function(notif) {
            // Update the score
            this.scoreCtrl[notif.args.player_id].incValue(notif.args.points);
        },

        notif_newTurn : function(notif) {
            this.turn_counter.incValue(1);
        },

        playCardOnTable: function(card, comm=false) {

        	var player_id = card['location_arg'];
        	var card_id = card['id'];
        	var preexist = $('card_' + card_id);

        	var target = 'playertablecard_'+this.players[player_id]['relative'];
        	if(comm)
        	{
        		target = 'comcard_'+player_id;
        	}

        	if(preexist)
        	{
        		this.attachToNewParent('card_' + card['id'], target);
        	}
        	else
        	{
        		dojo.place( this.format_block('jstpl_cardontable', card), $(target));
            	dojo.query('#card_' + card['id']).connect('onclick', this, 'onPlayCard');
        	}

            if (player_id != this.player_id) {
            	if(!preexist)
            	{
	                // Some opponent played a card
	                // Move card from player panel
	                this.placeOnObject('card_' + card['id'], 'playertable_' + player_id);
            	}
            }
            else {
                // You played a card. If it exists in your hand, move card from there and remove the corresponding item
                if($('myhand_item_' + card_id)) {
                    this.placeOnObject('card_' + card_id, 'myhand_item_' + card_id);
                    this.player_hand.removeFromStockById(card_id);
                }
            }

            dojo.animateProperty({
	       	    node: 'card_'+card_id,
	       	    duration: 1000,
	            easing: dojo.fx.easing.expoInOut,
	       	    properties: {
	               left: 0,
	               top: 0,
	       	    }

	       	  }).play();


            // Add tooltip
            this.createCardTooltip('card_' + card_id, card['type'], card['type_arg']);
        },

        /* This enable to inject translatable styled things to logs or action bar */
        /* @Override */
        format_string_recursive : function(log, args) {
            try {
                if (log && args && !args.processed) {


                    args.processed = true;

                    // Representation of the value of a card
                    if (args.value_symbol !== undefined) {
                        args.value_symbol = dojo.string.substitute("<strong class='${actual_color}'>${value_symbol}</strong>", {'actual_color' : this.colors[args.color_symbol].color, 'value_symbol' : args.value_symbol});
                    }

                    // Representation of the color of a card
                    if (args.color_symbol !== undefined) {
                        args.color_symbol = dojo.string.substitute("<span class='logicon ${actual_color}' title='${actual_color_name}'></span>", {'actual_color' : this.colors[args.color_symbol].color, 'actual_color_name' : this.colors[args.color_symbol].name});
                    }

                    if (args.color_name !== undefined) {
                         args.color_name = _(this.colors[args.color_name].name);
                    }

                    if (args.color_nameof !== undefined) {
                         args.color_nameof = _(this.colors[args.color_nameof].nameof);
                    }
                }
            } catch (e) {
                console.error(log,args,"Exception thrown", e.stack);
            }
            return this.inherited(arguments);
        },

        divYou : function() {
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
