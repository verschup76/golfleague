@extends('template.base')

@section('title')
    League Match: {{$date}}
@stop

@section('first-css')
    <link rel="stylesheet" href="<?php echo asset('jqGrid/css/ui.jqgrid.css')?>" />
	<style>
		a {
			color: #00A65A;
		}
	</style>
@stop

@section('page-header')


@stop

@section('breadcrumb')

@stop

@section('content')
	<!--
    <div class="row">
        <div class="col-md-6">
            <div class="box box-success collapsed-box">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{$course['name']}}</h3>
                        <div class="box-tools pull-right">
                            <button data-widget="collapse" class="btn btn-box-tool"><i class="fa fa-plus"></i></button>
                            <button data-widget="remove" class="btn btn-box-tool"><i class="fa fa-times"></i></button>
                        </div><!-- /.box-tools
                    </div><!-- /.box-header
                <div class="box-body table-responsive" style="display: none;">
                        <table id="courseInfo" class="display" cellspacing="0" width="100%">
                            <thead>
                            <tr>
                                <th>Hole </th>
                                <th>Yards</th>
                                <th>Handicap</th>
                            </tr>
                            </thead>
                        </table>
                    </div><!-- /.box-body
                </div>
        </div>{{-- end .col-md-3 --}}
    </div>{{-- end .row --}}
	-->


    <div class="row">
        <div class="col-md-6">
            <div class="box box-success" id="group1">
                <div class="box-header">
                  <h3 class="box-title"><a href="edit?group=1">Group 1</a></h3>
                </div><!-- /.box-header -->
                <div class="box-body table-responsive">
                    <table id="group1Grid"></table>
                    <div id="group1Pager"></div>
                </div><!-- /.box-body -->
                <div class="box-footer clearfix">
                </div>
            </div>

            <div class="box box-success" id="group2">
                <div class="box-header">
                  <h3 class="box-title"><a href="edit?group=2">Group 2</a></h3>
                </div><!-- /.box-header -->
                <div class="box-body table-responsive">
                    <table id="group2Grid"></table>
                    <div id="group2Pager"></div>
                </div><!-- /.box-body -->
                <div class="box-footer clearfix">
                </div>
            </div>

            <div class="box box-success" id="group3">
                <div class="box-header">
                  <h3 class="box-title"><a href="edit?group=3">Group 3</a></h3>
                </div><!-- /.box-header -->
                <div class="box-body table-responsive">
                    <table id="group3Grid"></table>
                    <div id="group3Pager"></div>
                </div><!-- /.box-body -->
                <div class="box-footer clearfix">
                </div>
            </div>
        </div>{{-- end .col-md-7 --}}
</div>{{-- end .row --}}

@stop

@section('include-js')

@stop

@section('page-js')
    <script src="<?php echo asset('LTE/plugins/jQueryUI/jquery-ui-1.10.3.min.js')?>" type="text/javascript"></script>
    <script src="<?php echo asset('jqGrid/js/i18n/grid.locale-en.js')?>" type="text/javascript"></script>
    <script src="<?php echo asset('jqGrid/js/jquery.jqGrid.min.js')?>" type="text/javascript"></script>


    <script>
        $(document).ready(function () {

            var tournamentId;

            var tournamentTable;

            function getTournamentId(){
                $.ajax({
                    url:    "{{URL::to('/')}}/tournament/{{$id}}",
                    type:   "get",
                    success: function(data) {
                        if(data[0].tournament_id){
                            tournamentId = data[0].tournament_id;
                            $('#tournamentLeaderboard').removeClass('hidden');

                            tournamentTable = $('#tournamentTable').DataTable({
                                "scrollY": "200px",
                                "bFilter": false,
                                "bPaginate": false,
                                "bInfo": false,
                                "bSort": false,
                                "bAutoWidth": false,
                                "ajax": {
                                    "url": "{{URL::to('/')}}/tournamentLeaderboard/" + tournamentId,
                                    "type": "GET"
                                },
                                "columns": [
                                    {"data": "name"},
                                    {
                                        "data": function (data) {
                                            if (data.score === 0) {
                                                return "E";
                                            }
                                            else
                                                return data.score;
                                        }
                                    }
                                ]
                            });


                        } else {
                            tournamentId = null;
                        }

                    },
                    error: function(data){
                        alert(data.message);
                    }
                });
            }

            tournamentId = getTournamentId();

            var grossTable = $('#grossTable').DataTable( {
                "scrollY":        "200px",
                "bFilter": false,
                "bPaginate": false,
                "bInfo": false,
                "bSort": false,
                "bAutoWidth": false,
                "ajax": {
                    "url": "{{URL::to('/')}}/liveleaderboard/gross?match={{$id}}",
                    "type": "GET"
                },
                "columns": [
                    { "data": "name" },
                    { "data": function(data){
                            if (data.score === 0) {
                                return "E";
                            }
                            else
                                return data.score;
                    }}
                ]
            });

            var netTable = $('#netTable').DataTable( {
                "scrollY":        "200px",
                "bFilter": false,
                "bPaginate": false,
                "bInfo": false,
                "bSort": false,
                "bAutoWidth": false,
                "ajax": {
                    "url": "{{URL::to('/')}}/liveleaderboard/net?match={{$id}}",
                    "type": "GET"
                },
                "columns": [
                    { "data": "name" },
                    { "data": function(data){
                            if (data.score === 0) {
                                return "E";
                            }
                            else
                                return data.score;
                    }}
                ]
            });
			$( "#leaderboard" ).click(function(){
                tournamentTable.ajax.reload();
				grossTable.ajax.reload();
				netTable.ajax.reload();
			});
            var groupId = get('group');
            var scores = {'': '','1':'1','2':'2','3':'3','4':'4','5':'5','6':'6','7':'7','8':'8','9':'9','10':'10','11':'11','12':'12'};
            for (i = 1; i < 4; i++) {
                if (groupId == i) {
                $("#group"+ i +"Grid").jqGrid({
                    url: '{{URL::to('/')}}/matchrounds/'+{{$id}} +'?group=' + i,
                    datatype: "json",
                    colModel: [
                        { label: 'Hole <br /> <font color="blue">Par</font>', name: 'name',  width: 80, frozen: true },
                        { label: 'Hole', index: 'score', name: 'round.0.holescores.0.score', width: 37, align: 'center', sorttype:"int", editable: true,
                            edittype: 'select',
                            editoptions : { value: scores,
                                            dataEvents : [
                                                { type: 'change', fn: function (e) {
                                                            getValue(e);
                                                        }
                                                }
                                            ]
                            }

                        },
                        { label: 'Hole', name: 'round.0.holescores.1.score', width: 37, align: 'center', editable: true,
                            edittype: 'select',
                            editoptions : { value: scores,
                                            dataEvents : [
                                                { type: 'change', fn: function (e) {
                                                            getValue(e);
                                                        }
                                                }
                                            ]
                            }
                        },
                        { label: 'Hole', name: 'round.0.holescores.2.score', width: 37, align: 'center', editable: true,
                            edittype: 'select',
                            editoptions : { value: scores,
                                            dataEvents : [
                                                { type: 'change', fn: function (e) {
                                                            getValue(e);
                                                        }
                                                }
                                            ]
                            }
                        },
                        { label: 'Hole', name: 'round.0.holescores.3.score', width: 37, align: 'center', editable: true,
                            edittype: 'select',
                            editoptions : { value: scores,
                                            dataEvents : [
                                                { type: 'change', fn: function (e) {
                                                            getValue(e);
                                                        }
                                                }
                                            ]
                            }
                        },
                        { label: 'Hole', name: 'round.0.holescores.4.score', width: 37, align: 'center', editable: true,
                            edittype: 'select',
                            editoptions : { value: scores,
                                            dataEvents : [
                                                { type: 'change', fn: function (e) {
                                                            getValue(e);
                                                        }
                                                }
                                            ]
                            }
                        },
                        { label: 'Hole', name: 'round.0.holescores.5.score', width: 37, align: 'center', editable: true,
                            edittype: 'select',
                            editoptions : { value: scores,
                                            dataEvents : [
                                                { type: 'change', fn: function (e) {
                                                            getValue(e);
                                                        }
                                                }
                                            ]
                            }
                        },
                        { label: 'Hole', name: 'round.0.holescores.6.score', width: 37, align: 'center', editable: true,
                            edittype: 'select',
                            editoptions : { value: scores,
                                            dataEvents : [
                                                { type: 'change', fn: function (e) {
                                                            getValue(e);
                                                        }
                                                }
                                            ]
                            }
                        },
                        { label: 'Hole', name: 'round.0.holescores.7.score', width: 37, align: 'center', editable: true,
                            edittype: 'select',
                            editoptions : { value: scores,
                                            dataEvents : [
                                                { type: 'change', fn: function (e) {
                                                            getValue(e);
                                                        }
                                                }
                                            ]
                            }
                        },
                        { label: 'Hole', name: 'round.0.holescores.8.score', width: 37, align: 'center', editable: true,
                            edittype: 'select',
                            editoptions : { value: scores,
                                            dataEvents : [
                                                { type: 'change', fn: function (e) {
                                                            getValue(e);
                                                        }
                                                }
                                            ]
                            }
                        },
                        { label: '<br />Total', name: 'round.0.score', width: 37, align: 'center'}

                    ],
                    width: 470,
                    height: 100,
                    loadComplete: function (data) {

                        var $this = $(this), ids = $this.jqGrid('getDataIDs'), j, l = ids.length;
                        for (j = 0; j < l; j++) {
                            $this.jqGrid('editRow', ids[j], true);

                            //For each cell in this row edit the id
                            for (k = 0; k < 9; k++) {
                                var player = data[j].id;
                                var oldId = player + '_round\\.0\\.holescores\\.'+ k +'\\.score';
                                var newId = data[j].round[0].holescores[k].id;

                                $("#"+oldId).attr('id', newId);
                                $("#"+newId).attr('name', 'score');
                            }
                        }
						//use data to populate the course holes and par
						$.each(data[0].round[0].course.holes, function(key, value) {
							var name = 'round.0.holescores.' + key + '.score';
							var par = value.par;
							var label = value.number + '<br /><font color="blue">' + par + '</font>';
							$this.jqGrid('setLabel', name, label );
						});
                    }

                    });
                }
                else{

                    $("#group"+ i +"Grid").jqGrid({
                    url: '{{URL::to('/')}}/matchrounds/'+{{$id}} +'?group=' + i,
                    datatype: "json",
                    colModel: [
                        { label: 'Hole <br /> <font color="blue">Par</font>', name: 'name', height:50, width: 80, frozen: true },
                        { label: 'Hole', name: 'round.0.holescores.0.score', width: 37, align: 'center'},
                        { label: 'Hole', name: 'round.0.holescores.1.score', width: 37, align: 'center' },
                        { label: 'Hole', name: 'round.0.holescores.2.score', width: 37, align: 'center' },
                        { label: 'Hole', name: 'round.0.holescores.3.score', width: 37, align: 'center' },
                        { label: 'Hole', name: 'round.0.holescores.4.score', width: 37, align: 'center' },
                        { label: 'Hole', name: 'round.0.holescores.5.score', width: 37, align: 'center' },
                        { label: 'Hole', name: 'round.0.holescores.6.score', width: 37, align: 'center' },
                        { label: 'Hole', name: 'round.0.holescores.7.score', width: 37, align: 'center' },
                        { label: 'Hole', name: 'round.0.holescores.8.score', width: 37, align: 'center' },
                        { label: '<br />Total', name: 'round.0.score', width: 37, align: 'center'}

                    ],
                    viewrecords: true,
                    width: 470,
                    height: 100,
					loadComplete: function (data) {

						var $this = $(this), ids = $this.jqGrid('getDataIDs'), j, l = ids.length;
						$("table.ui-jqgrid-htable th div").css ("height", 30);
						//use data to populate the course holes and par
						$.each(data[0].round[0].course.holes, function(key, value) {
							var name = 'round.0.holescores.' + key + '.score';
							var par = value.par;
							var label = value.number + '<br /><font color="blue">' + par + '</font>';
							$this.jqGrid('setLabel', name, label );
						});
                    }
                });

                }
            }
            $( "#group" + groupId ).insertBefore( $( "#group1"));
            function getValue(e) {
                var currentId = $(e.target).attr('id');
                $.ajax({
                beforeSend: function() {
                    $('input[type="text"], input[type="checkbox"], select').prop("disabled", true);
                },
                type: 'PUT',
                url: '{{URL::to('/')}}/holescores/' + currentId,
                data: $(e.target).serialize()
                }).done(function() {
                    //var name = $(e.target).val();
                    //show something that says score posted
                    $("#group1Grid" ).trigger("reloadGrid");
                    $("#group2Grid" ).trigger("reloadGrid");
                    $("#group3Grid" ).trigger("reloadGrid");
                    grossTable.ajax.reload();
                    netTable.ajax.reload();
                });
            }
        });
                /* uncomment in production for refresh grid
                intervalId = setInterval(
                        function() {
                            $("#group1Grid" ).trigger("reloadGrid");
                            $("#group2Grid" ).trigger("reloadGrid");
                            $("#group3Grid" ).trigger("reloadGrid");
                        },
                        30000); // 300 sec === 5 min
                */


        function get(name){
            if(name=(new RegExp('[?&]'+encodeURIComponent(name)+'=([^&]*)')).exec(location.search))
                return decodeURIComponent(name[1]);
        }


    </script>

@stop

@section('onload')
<script>
    $(document).ready(function() {
		$( "#leaderboard" ).show();
    });
</script>
    @stop
