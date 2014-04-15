<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo asset('jquery-ui-1.10.4.custom/css/blitzer/jquery-ui-1.10.4.custom.css')?>" />
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo asset('jquery.jqGrid-4.5.4/css/ui.jqgrid.css')?>" />
</head>
<body>
    <table id="players"></table>
    <div id="playersPager"></div>
	<br><br>
	<table id="courses"></table>
    <div id="coursesPager"></div>
	<br><br>
	<table id="matches"></table>
    <div id="matchesPager"></div>
	
<script src="<?php echo asset('jquery.jqGrid-4.5.4/js/jquery-1.9.0.min.js')?>" type="text/javascript"></script>
<script src="<?php echo asset('jquery.jqGrid-4.5.4/js/i18n/grid.locale-en.js')?>" type="text/javascript"></script>
<script src="<?php echo asset('jquery.jqGrid-4.5.4/js/jquery.jqGrid.min.js')?>" type="text/javascript"></script>

<script type="text/javascript">
//jqGrid code for the Players section
$(function () {
    $("#players").jqGrid({
        url: "players/getPlayers",
        datatype: "json",
        mtype: "GET",
        colNames: ["Name", "Handicap"],
        colModel: [
            { name: "name", width: 375, editable:true },
            { name: "handicap", width: 100 }
        ],
        pager: "#playersPager",
        rowNum: 10,
        rowList: [10, 20, 30],
        sortname: "name",
        sortorder: "desc",
        viewrecords: true,
        gridview: true,
        autoencode: true,
        caption: "Players",
        editurl:"players/edit"
    });
    $("#players").jqGrid("navGrid", "#playersPager",{edit:true,add:true,del:true,refresh:false},
        {
        closeAfterEdit:true,
        recreateForm: true,
        editCaption: "Edit Player",
        errorTextFormat:function(data){
            return data.statusText;
        },
            reloadAfterSubmit:true
        },
        {
            closeAfterAdd:true,
            recreateForm: true,
            addCaption: "Add Player",
            errorTextFormat:function(data){
                return data.statusText;
            },
            reloadAfterSubmit:true,
            }
    );
});

//jqGrid code for the Courses section
$(function () {
    $("#courses").jqGrid({
        url: "courses/getCourses",
        datatype: "json",
        mtype: "GET",
        colNames: ["Name", "Par", "Rating", "Slope"],
        colModel: [
            { name: "name", width: 175, editable:true },
			{ name: "par", width: 100, editable:true },
            { name: "rating", width: 100, editable:true },
			{ name: "slope", width: 100, editable:true },		
        ],
        pager: "#coursesPager",
        rowNum: 10,
        rowList: [10, 20, 30],
        sortname: "name",
        sortorder: "desc",
        viewrecords: true,
        gridview: true,
        autoencode: true,
        caption: "Courses",
        editurl:"courses/edit"
    });
    $("#courses").jqGrid("navGrid", "#coursesPager",{edit:true,add:true,del:true,refresh:false},
        {
        closeAfterEdit:true,
        recreateForm: true,
        editCaption: "Edit Course",
        errorTextFormat:function(data){
            return data.statusText;
        },
            reloadAfterSubmit:true
        },
        {
            closeAfterAdd:true,
            recreateForm: true,
            addCaption: "Add Course",
            errorTextFormat:function(data){
                return data.statusText;
            },
            reloadAfterSubmit:true,
            }
    );
});

//jqGrid code for the Matches section
$(function () {
    $("#matches").jqGrid({
        url: "matches/getMatches",
        datatype: "json",
        mtype: "GET",
        colNames: ["Player 1 Team 1", "Player 2 Team 1", "Player 1 Team 2", "Player 2 Team 2", "Course", "Season", "date"],
        colModel: [
            { name: "player_1_team_1", width: 100, editable:true },
            { name: "player_2_team_1", width: 100, editable:true },
			{ name: "player_1_team_2", width: 100, editable:true },
			{ name: "player_2_team_2", width: 100, editable:true },
			{ name: "course_id", width: 100, editable:true },
			{ name: "season_id", width: 75, editable:true },
			{ name: "date", width: 75, editable:true }			
        ],
        pager: "#matchesPager",
        rowNum: 10,
        rowList: [10, 20, 30],
        sortname: "name",
        sortorder: "desc",
        viewrecords: true,
        gridview: true,
        autoencode: true,
        caption: "Matches",
        editurl:"matches/edit"
    });
    $("#matches").jqGrid("navGrid", "#matchesPager",{edit:true,add:true,del:true,refresh:false},
        {
        closeAfterEdit:true,
        recreateForm: true,
        editCaption: "Edit Match",
        errorTextFormat:function(data){
            return data.statusText;
        },
            reloadAfterSubmit:true
        },
        {
            closeAfterAdd:true,
            recreateForm: true,
            addCaption: "Add Match",
            errorTextFormat:function(data){
                return data.statusText;
            },
            reloadAfterSubmit:true,
            }
    );
});



</script>	
</body>
</html>



