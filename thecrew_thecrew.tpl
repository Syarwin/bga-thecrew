{OVERALL_GAME_HEADER}


<div id="thecrew-table" data-players="{NBR}">
  <div id="jarvis-hand-container"><div id="jarvis-hand"></div></div>
	<div id="table-top"></div>

	<div id="table-middle">


    <div id="mission-status" class="book">
      <section class="open-book">
        <header></header>
        <article>
          <div id="mission-page-left">
            <h2 class="chapter-title" id='mission-counter-wrapper'>
              <span id="mission-title">{MISSION}</span>
              <span id='mission-counter'></span>
            </h2>

            <div id='mission-description'></div>
          </div>

          <div id="mission-page-right">
            <div id='mission-informations' class='mission-informations'></div>
            <div id="distress"></div>
          </div>
        </article>
        <footer>
          <div id='try-counter-wrapper'>
            <span>{TRY}</span>
            <span id='try-counter'></span>
          </div>

          <div id='total-try-counter-wrapper'>
            <span>{TOTALTRY}</span>
            <span id='total-try-counter'></span>
          </div>
        </footer>
      </section>
    </div>


    <div id="cards-mat">
      <div id="card-mat-top"></div>
      <div id="card-mat-bottom"></div>
    </div>

    <div id="tasks">
	    <h2>
        {TASKS}
      </h2>
      <div id="tasks-list-container">
        <div id="tasks-list"></div>
      </div>
    </div>


    <div id="end-panel">
      <div id="end-panel-top"></div>

      <div id="end-panel-middle">
        <h2 id="endResult"></h2>
        <h2 id="endResultMessage"></h2>

        <div id="end-panel-buttons">
          <button id="yes-button" class="finalbutton bgabutton bgabutton_blue"></button>
          <button id="no-button" class="bgabutton bgabutton_gray"></button>
        </div>
      </div>

      <div id="end-panel-bottom"></div>
	  </div>


    <div id="distress-panel">
      <div id="distress-panel-top"></div>

      <div id="distress-panel-middle">
        <div id="distress-panel-icon"></div>

        <h2>
          {DISTRESS}
        </h2>

        <div id="distress-panel-buttons">
          <button id="clockwise-button" class="finalbutton bgabutton bgabutton_blue"><div></div></button>
          <button id="dont-use-button" class="finalbutton bgabutton bgabutton_gray">{NO}</button>
          <button id="whatever-button" class="finalbutton bgabutton bgabutton_gray">{WHATEVER}</button>
          <button id="anticlockwise-button" class="finalbutton bgabutton bgabutton_blue"><div></div></button>
        </div>

        <div id="distress-panel-direction"></div>
      </div>

      <div id="distress-panel-bottom"></div>
	  </div>

    <div id="fail-mission-panel">
      <div id="fail-mission-panel-top"></div>

      <div id="fail-mission-panel-middle">

        <h2>
          {RESTART_MISSION}
        </h2>

        <div id="fail-mission-panel-buttons">
          <button id="fail-mission-agree-button" class="finalbutton bgabutton bgabutton_blue">{YES}</button>
          <button id="fail-mission-dont-want-button" class="finalbutton bgabutton bgabutton_red">{NO}</button>
        </div>

        <div id="fail-mission-panel-answer"></div>
      </div>

      <div id="fail-mission-panel-bottom"></div>
	  </div>


    <div id="give-task-panel">
      <div id="give-task-panel-top"></div>

      <div id="give-task-panel-middle">
        <div id="give-task-proposal">
          <div id="proposal-task"></div>
          <div id="proposal-transaction">
            <div id="proposal-source"></div>
            <div class="thecrew-arrow-down"></div>
            <div id="proposal-target"></div>
          </div>
        </div>

        <div id="give-task-buttons">
          <button id="agree-button" class="finalbutton bgabutton bgabutton_blue">{YES}</button>
          <button id="reject-button" class="finalbutton bgabutton bgabutton_gray">{NO}</button>
        </div>
      </div>

      <div id="give-task-panel-bottom"></div>
	  </div>

  </div>

  <div id="table-bottom"></div>
</div>

<div id="hand-container"></div>

<div id="discard-container">
  <div id="discard-wrapper">
    <div id="discard-grid"></div>
  </div>
</div>

<script type="text/javascript">
// Javascript HTML templates
/*
 * PLAYER SPECIFIC
 */
var jstpl_playerMat = '<div id="mat-${id}" data-no="${no}" class="mat-card-holder"></div>';
var jstpl_playerCheckMark = '<div class="check-ok" data-no="${no}" id="continue-ok-${id}"></div>';
var jstpl_playerCheckMarkGiveTask = '<div class="check-ok" data-no="${no}" id="give-task-ok-${id}"></div>';
var jstpl_playerDistressChoice = '<div class="distress-choice" data-no="${no}" data-choice="${distressChoice}" id="distress-choice-${id}"></div>';
var jstpl_playerFailMissionAnswer = '<div class="fail-mission-answer" data-no="${no}" data-answer="${restartMissionAnswer}" id="fail-mission-answer-${id}"></div>';
var jstpl_playerPanel = `
<div class="panel-container" id="panel-container-\${id}" data-no="\${no}">
  <div class="panel-tricks" id="tricks-\${id}">
    <div></div>
    <span id="trick-counter-\${id}">0</span>
  </div>

  <div class="panel-cards" id="cards-in-hand-\${id}">
    <div></div>
    <span id="cards-in-hands-counter-\${id}">0</span>
  </div>

  <div class="panel-commander" id="panel-commander-\${id}"></div>
</div>`;

var jstpl_playerTable = `
<div id="player-table-\${id}" class="player-table" data-no="\${no}">
  <div class="communication-card-holder">
    <div class="player-reply">
      <div class="bubble" id="reply-\${id}"></div>
    </div>
    <div id="comm-card-\${id}" class="communication-card" data-color="6" data-value="">
      <div class="radio middle" id="radio-\${id}"></div>
      <div class="disruption" id="disruption-\${id}"><i class="fa fa-bolt" aria-hidden="true"></i></div>
      <div class="pending-icon" id="comm-pending-\${id}"><svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="hand-point-up" class="svg-inline--fa fa-hand-point-up fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M105.6 83.2v86.177a115.52 115.52 0 0 0-22.4-2.176c-47.914 0-83.2 35.072-83.2 92 0 45.314 48.537 57.002 78.784 75.707 12.413 7.735 23.317 16.994 33.253 25.851l.146.131.148.129C129.807 376.338 136 384.236 136 391.2v2.679c-4.952 5.747-8 13.536-8 22.12v64c0 17.673 12.894 32 28.8 32h230.4c15.906 0 28.8-14.327 28.8-32v-64c0-8.584-3.048-16.373-8-22.12V391.2c0-28.688 40-67.137 40-127.2v-21.299c0-62.542-38.658-98.8-91.145-99.94-17.813-12.482-40.785-18.491-62.791-15.985A93.148 93.148 0 0 0 272 118.847V83.2C272 37.765 234.416 0 188.8 0c-45.099 0-83.2 38.101-83.2 83.2zm118.4 0v91.026c14.669-12.837 42.825-14.415 61.05 4.95 19.646-11.227 45.624-1.687 53.625 12.925 39.128-6.524 61.325 10.076 61.325 50.6V264c0 45.491-35.913 77.21-39.676 120H183.571c-2.964-25.239-21.222-42.966-39.596-59.075-12.65-11.275-25.3-21.725-39.875-30.799C80.712 279.645 48 267.994 48 259.2c0-23.375 8.8-44 35.2-44 35.2 0 53.075 26.4 70.4 26.4V83.2c0-18.425 16.5-35.2 35.2-35.2 18.975 0 35.2 16.225 35.2 35.2zM352 424c13.255 0 24 10.745 24 24s-10.745 24-24 24-24-10.745-24-24 10.745-24 24-24z"></path></svg></div>
      <div class="communication-waiting">
        <div class="wifi-symbol">
          <div class="wifi-circle first"></div>
          <div class="wifi-circle second"></div>
          <div class="wifi-circle third"></div>
          <div class="wifi-circle fourth"></div>
        </div>
      </div>
    </div>
  </div>

  <div class="player-table-wrapper">
    <div id="player-table-name-\${id}" class="player-table-name" style="color:#\${color}">
      \${name}
    </div>

    <div id="player-table-missions-\${id}" class="player-table-mission">
      <div id="commander-icon-\${id}" class="commander"></div>
      <div id="special-icon-\${id}" class="special"></div>
      <div id="special2-icon-\${id}" class="special2"></div>
    </div>
  </div>
</div>`;


var jstpl_task = `
<div id="task-\${id}" class="task" data-color="\${color}" data-value="\${value}" data-status="\${status}" data-tile="\${tile}">
  <div class="task-inner">
    <div class="task-tile \${tileClass}"></div>
    <div class="check-ok"></div>
  </div>
</div>`;

var jstpl_card = `
<div class="card" data-color="\${color}" data-value="\${value}" id="card-\${id}"></div>`;



var jstpl_configPlayerBoard = `
<div class='player-board' id="player_board_config">
  <div id="player_config" class="player_board_content">
    <div id="fail-mission-button">
      <button style="width: auto;" class="action-button bgabutton bgabutton_small bgabutton_red" id="bga-thecrew-fail-mission">
        \${failMission}
      </button>
    </div>
    <div id="autopick-selector">
        \${autopick}
        <select id="autopick">
          <option value='0'>\${disabled}</option>
          <option value='2'>\${alwaysno}</option>
          <option value='4'>\${alwaysagree}</option>
        </select>
    </div>

    <div id="autocontinue-selector">
        \${autocontinue}
        <select id="autocontinue">
          <option value='0'>\${disabled}</option>
          <option value='1'>\${alwaysyes}</option>
        </select>
    </div>

    <div id="confirmpreselect-selector">
        \${confirmpreselect}
        <select id="confirmpreselect">
          <option value='0'>\${showconfirm}</option>
          <option value='1'>\${skipconfirm}</option>
        </select>
    </div>

  </div>
</div>
`;

var jstpl_jarvis = `<div id="overall_player_board_1" class="player-board" style="border-color: rgb(255, 0, 0); width: 234px; height: auto;">
                        <div class="player_board_inner" id="player_board_inner_ff0000">
                            <div class="emblemwrap" id="avatarwrap_1">
                                <div class="emblempremium"></div>
                            </div>
                            <div id="rtc_placeholder_1" class="rtc_placeholder"></div>
                            <div class="emblemwrap" id="avatar_active_wrap_1" style="display:none">
                                <div class="icon20 icon20_night this_is_night hidden"></div>
                            </div>
                            <div class="player-name" id="player_name_1">
                            	<a  style="color: #ff0000" target="_blank">Jarvis</a>
                            	<i id="player_1_status" class="fa fa-circle status_offline player_status hidden"></i>
                            	<div class="flag" style="background-position: -112px -66px; display:none;" title="France"></div>
                            </div>
                            <div id="player_board_1" class="player_board_content">
                                <div class="player_score">
                                    <span id="player_score_1" class="player_score_value"></span> <i class="fa fa-star" id="icon_point_1"></i>
                                    <span class="player_elo_wrap">• <div class="gamerank gamerank_beginner "><span class="icon20 icon20_rankw"></span> <span class="gamerank_value" id="player_elo_1" "="">0</span></div></span>
                                    <span id="timeToThink_1" class="timeToThink">--:--</span>
                                </div>
                                <div class="player_showcursor" id="player_showcursor_1"><input type="checkbox" checked="checked" class="player_hidecursor" id="player_hidecursor_1"> Show cursor <i class="fa fa-hand-pointer-o" style="color:#ff0000"></i></div>
                                <div class="player_table_status" id="player_table_status_1"></div>
</div>
                            <div id="player_panel_content_ff0000" class="player_panel_content">
                            </div>
                        </div>
                    </div>
`;


</script>

{OVERALL_GAME_FOOTER}
