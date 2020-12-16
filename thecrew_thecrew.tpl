{OVERALL_GAME_HEADER}


<div id="up">
    <div id="table" class="{NBR}">
    	<div id="row1" class="frow"></div>
    	<div id="row2" class="frow">
    	
	    <div id="left">
	        <div id='turn_count_wrap'><span>{MISSION}</span> <span id='mission_counter'></span></span></div>
	        <div id='mission_description'></div>
	     	<div id='try_wrap'><b><span>{TRY}</span> <span id='try_counter'></span></span></b></div>
	     	<div id='total_try_wrap'><b><span>{TOTALTRY}</span> <span id='total_try_counter'></span></span></b></div>
	        <div id="distress" class=""></div>
	    </div>
    
		    <div id="playertable_central" class="centraltable whiteblock hidden">
		    	<div id="trow1" class="frow"></div>
		    	<div id="trow3" class="frow"></div>
		    </div>
		        
		    <div id="tasks" class="tasks whiteblock hidden">
		    <div class="playertablename">
                    {TASKS}	                
            </div>
                <div id="tasklists" class = "playertablecards">
		    	
                </div>
		    </div> 
		    
		    <div id="endPanel" class="tasks whiteblock hidden">		    
			    <div id="erow1" class="frow">
			    </div>
			    <div id="endPanelMiddle">
				    <div class="playertablename" id="endResult">
		            </div>
		            <div class="playertablename">
		                 	 {CONTINUE}               
		            </div>
	                <div class = "yesnoend">
			    		<a id="yes_button" class="finalbutton bgabutton bgabutton_blue" href="#" target=_"blank">{YES}</a><a id="no_button" class="finalbutton bgabutton bgabutton_gray" href="#">{NO}</a>
	                </div>
			    </div> 
			    
			    <div id="erow3" class="frow">			    
			    </div>
		    </div>
        </div>

    </div>
    	<div id="row3" class="frow"></div>
</div>

<div id="myhand_wrap" class="whiteblock">
    <div id="myhand">
    </div>
</div>

<script type="text/javascript">
    // Javascript HTML templates
    var jstpl_task = '<div class="taskontable col${card_type} val${card_type_arg}" id="task_${task_id}">\
		                		<div class="task_marker task${token}" id="marker_${task_id}"></div>\
		                		<div class="check_ok ${status}" id="status_${task_id}"></div>\
		                	</div>';
	var jstpl_cardontable = '<div class="cardontable col${type} val${type_arg}" id="card_${id}"></div>';	
	var jstpl_player = '<div class="panel_container" id="panel_container_${player_id}"><div class="tricks" id="tricks_${player_id}"><div class="icon trick"></div><span class="trick_number"><span class="times">&times;</span> <span id="trick_counter_${player_id}"></span></span></div><div class="cardsinhands" id="cardsinhand_${player_id}"><div class="icon cardsinhand"></div><span class="trick_number"><span class="times">&times;</span> <span id="cardsinhands_counter_${player_id}"></span></span></div><div class="commander_in_panel" id="commander_in_panel_${player_id}"></div></div>';
var jstpl_tooltip_common = '<div class="tooltip-container">\
		<span class="tooltip-title">${title}</span>\
		<hr/>\
		<span class="tooltip-message">${description}</span>\
	</div>';
var jstpl_temp_comm = '<div class="radio selectable radio_temp ${status}" id="radio_${player_id}"></div>';
var jstpl_player_board = '<div id="playertable_${id}" class="playertable whiteblock playertable_${relative}">\
                <div class="playertablename" style="color:#${player_color}">\
                    ${player_name}\
                </div>\
                <div id = "tasks_${id}" class = "playertablecards">\
                	<div id="comcard_${id}" class="card_com" >\
                		<div class="radio middle" id="radio_${id}"></div>\
                	</div>\
                	<div id="commander_icon_spot_${id}" class="commander appears"></div>\
                	<div id="special_icon_spot_${id}" class="special appears"></div>\
                	<div id="special2_icon_spot_${id}" class="special2 appears"></div>\
                </div>\
            </div>';
var jstpl_player_table_card = '<div id="playertablecard_${relative}" class="playertablecard"></div>';
var jstpl_player_check_ok = '<div class="check_ok" id="continue_ok_${id}"></div>';
</script>

{OVERALL_GAME_FOOTER}
