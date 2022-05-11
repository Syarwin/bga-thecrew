define(["dojo", "dojo/_base/declare"], (dojo, declare) => {
  const WANT_RESTART_MISSION = 1, DONT_WANT_RESTART_MISSION = 2;
  return declare("thecrew.restartMissionTrait", null, {
    constructor(){
      this._notifications.push(
        ['chooseRestartMission', 10],
        ['answerRestartMission', 10],
        ['restartMissionActivated', 10],
      );
    },

    onEnteringStateRestartMissionSetup(args){
      debug("Entering state restart mission setup", args);
      this.switchCentralZone('restart-mission');
      dojo.attr('restart-mission-panel', 'data-answer', 0);
      this.addSecondaryActionButton('restartMissionHelp', '?', () => this.showMessage(_('A restart mission has been requested. If everyone agrees to restart this mission, this mission will set as failed. Everyone has to agree to restart this mission!'), 'info') );
      this.addTooltip('restartMissionHelp', _('A restart mission has been requested. If everyone agrees to restart this mission, this mission will set as failed. Everyone has to agree to restart this mission!'), '');

      if(!this.isSpectator){
        this.connect($('restart-mission-agree-button'),  'click', () => this.onAnswerRestartMission(WANT_RESTART_MISSION) );
        this.connect($('restart-mission-dont-want-button'),  'click', () => this.onAnswerRestartMission(DONT_WANT_RESTART_MISSION) );
      }

      Object.keys(args.players).forEach(pId => dojo.attr('restart-mission-answer-' + pId, 'data-answer', args.players[pId]) );
    },

    onAnswerRestartMission(answer){
      this.takeAction("actAnswerRestartMission", { answer });
    },

    notif_chooseRestartMission(n){
      debug("Choosing to restart mission", n);
      dojo.attr('restart-mission-answer-' + n.args.player_id, 'data-answer', n.args.answer);
    },

    notif_answerRestartMission(n){
      debug("Answering to restart mission request", n);
      dojo.attr('restart-mission-answer-' + n.args.pId, 'data-answer', n.args.answer);
    },

    notif_restartMissionActivated(n){
      if(n.args.answer != DONT_WANT_RESTART_MISSION){
        this.gamedatas.status.restartMission = true;
        this.updateMissionStatus();
      }
    },
  });
});
