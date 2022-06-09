define(["dojo", "dojo/_base/declare"], (dojo, declare) => {
  const WANT_FAIL_MISSION = 1, DONT_WANT_FAIL_MISSION = 2;
  return declare("thecrew.restartMissionTrait", null, {
    constructor(){
      this._notifications.push(
        ['chooseRestartMission', 10],
        ['answerRestartMission', 10],
        ['restartMissionActivated', 10],
      );
    },

    onEnteringStateRestartMissionSetup(args){
      debug("Entering state fail mission setup", args);
      this.switchCentralZone('fail-mission');
      dojo.attr('fail-mission-panel', 'data-answer', 0);
      this.addSecondaryActionButton('restartMissionHelp', '?', () => this.showMessage(_('A fail mission has been requested. Everyone has to agree to fail this mission!'), 'info') );
      this.addTooltip('restartMissionHelp', _('A fail mission has been requested. Everyone has to agree to fail this mission!'), '');

      if(!this.isSpectator){
        this.connect($('fail-mission-agree-button'),  'click', () => this.onAnswerRestartMission(WANT_FAIL_MISSION) );
        this.connect($('fail-mission-dont-want-button'),  'click', () => this.onAnswerRestartMission(DONT_WANT_FAIL_MISSION) );
      }

      Object.keys(args.players).forEach(pId => dojo.attr('fail-mission-answer-' + pId, 'data-answer', args.players[pId]) );
    },

    onAnswerRestartMission(answer){
      this.takeAction("actAnswerRestartMission", { answer });
    },

    notif_chooseRestartMission(n){
      debug("Choosing to restart mission", n);
      dojo.attr('fail-mission-answer-' + n.args.player_id, 'data-answer', n.args.answer);
    },

    notif_answerRestartMission(n){
      debug("Answering to restart mission request", n);
      dojo.attr('fail-mission-answer-' + n.args.pId, 'data-answer', n.args.answer);
    },

    notif_restartMissionActivated(n){
      if(n.args.answer != DONT_WANT_FAIL_MISSION){
        this.gamedatas.status.restartMission = true;
        this.updateMissionStatus();
      }
    },
  });
});
