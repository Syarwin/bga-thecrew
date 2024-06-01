define(["dojo", "dojo/_base/declare"], (dojo, declare) => {
  const WANT_FAIL_MISSION = 1, DONT_WANT_FAIL_MISSION = 2;
  return declare("thecrew.failMissionTrait", null, {
    constructor(){
      this._notifications.push(
        ['chooseFailMission', 10],
        ['answerFailMission', 10],
        ['failMissionActivated', 10],
      );
    },

    onEnteringStateFailMissionSetup(args){
      debug("Entering state fail mission setup", args);
      this.switchCentralZone('fail-mission');
      dojo.attr('fail-mission-panel', 'data-answer', 0);
      this.addSecondaryActionButton('failMissionHelp', '?', () => this.showMessage(_('A fail mission has been requested. If everyone agrees to restart this mission, this mission will set as failed. Everyone has to agree to restart this mission!'), 'info') );
      this.addTooltip('failMissionHelp', _('A fail mission has been requested. If everyone agrees to restart this mission, this mission will set as failed. Everyone has to agree to restart this mission!'), '');

      if(!this.isSpectator){
        this.connect($('fail-mission-agree-button'),  'click', () => this.onAnswerFailMission(WANT_FAIL_MISSION) );
        this.connect($('fail-mission-dont-want-button'),  'click', () => this.onAnswerFailMission(DONT_WANT_FAIL_MISSION) );
      }

      Object.keys(args.players).forEach(pId => dojo.attr('fail-mission-answer-' + pId, 'data-answer', args.players[pId]) );
    },

    onAnswerFailMission(answer){
      this.takeAction("actAnswerRestartMission", { answer });
    },

    notif_chooseFailMission(n){
      debug("Choosing to fail mission", n);
      dojo.attr('fail-mission-answer-' + n.args.player_id, 'data-answer', n.args.answer);
    },

    notif_answerFailMission(n){
      debug("Answering to fail mission request", n);
      dojo.attr('fail-mission-answer-' + n.args.pId, 'data-answer', n.args.answer);
    },

    notif_failMissionActivated(n){
      if(n.args.answer != DONT_WANT_FAIL_MISSION){
        this.gamedatas.status.failMission = true;
        this.updateMissionStatus();
      }
    },
  });
});
