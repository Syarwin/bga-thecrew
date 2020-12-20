{OVERALL_GAME_HEADER}


<div id="thecrew-table" data-players="{NBR}">
	<div id="table-top"></div>

	<div id="table-middle">
    <div id="mission-status">
      <div id='mission-counter-wrapper'>
        <span>{MISSION}</span>
        <span id='mission-counter'></span>
      </div>

      <div id='mission-description'></div>

     	<div id='try-counter-wrapper'>
        <span>{TRY}</span>
        <span id='try-counter'></span>
      </div>

     	<div id='total-try-counter-wrapper'>
        <span>{TOTALTRY}</span>
        <span id='total-try-counter'></span>
      </div>

      <div id="distress"></div>
    </div>


    <div id="cards-mat">
      <div id="card-mat-top"></div>
      <div id="card-mat-bottom"></div>
    </div>

    <div id="tasks">
	    <h2>
        {TASKS}
      </h2>
      <div id="tasks-list"></div>
    </div>


    <div id="end-panel">
      <div id="end-panel-top"></div>

      <div id="end-panel-middle">
        <div class="playertablename" id="endResult"></div>

        <div class="playertablename">
          {CONTINUE}
        </div>

        <div id="end-panel-buttons">
          <button id="yes-button" class="finalbutton bgabutton bgabutton_blue">{YES}</button>
          <button id="no-button" class="finalbutton bgabutton bgabutton_gray">{NO}</button>
        </div>
      </div>

      <div id="end-panel-bottom"></div>
	  </div>
  </div>

  <div id="table-bottom"></div>
</div>

<div id="hand-container"></div>

<script type="text/javascript">
// Javascript HTML templates
/*
 * PLAYER SPECIFIC
 */
var jstpl_playerMat = '<div id="mat-${no}" class="mat-card-holder"></div>';
var jstpl_playerCheckMark = '<div class="check-ok" id="continue-ok-${id}"></div>';
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
  <div class="player-table-name" style="color:#\${color}">
    \${name}
  </div>
  <div class="player-table-wrapper">
    <div id="comcard-\${id}" class="communication-card">
      <div class="radio middle" id="radio-\${id}"></div>
    </div>

    <div id="commander-icon-\${id}" class="commander"></div>
    <div id="special-icon-\${id}" class="special"></div>
    <div id="special2-icon-\${id}" class="special2"></div>
    <div id="tasks-\${id}"></div>
  </div>
</div>`;


var jstpl_task = `
<div class="task" data-color="\${color}" data-value="\${value}" id="task-\${id}">
  <div class="task-tile \${tileClass}"></div>
  <div class="check-ok \${status}"></div>
</div>`;

var jstpl_card = `
<div class="card" data-color="\${color}" data-value="\${value}" id="card-\${id}"></div>`;


/*


var jstpl_temp_comm = '<div class="radio selectable radio_temp ${status}" id="radio_${player_id}"></div>';
*/
</script>

{OVERALL_GAME_FOOTER}
