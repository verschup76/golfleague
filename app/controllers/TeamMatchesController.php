<?php

use GolfLeague\Services\MatchService as MatchService;
use GolfLeague\Storage\MatchRound\MatchRoundRepository as MatchRoundRepository;
use GolfLeague\Storage\Team\TeamMatchesRepository as TeamMatchesRepository;

class TeamMatchesController extends \BaseController {

    public function __construct(MatchService $match, MatchRoundRepository $matchRoundRepo, TeamMatchesRepository $teamMatchesRepository)
    {
        $this->match = $match;
        $this->matchRoundRepo = $matchRoundRepo;
        $this->teamMatchesRepo = $teamMatchesRepository;
    }

    /**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
        $group = Input::get('group');  // Stay in controller


        $groupPlayers = $this->matchRoundRepo->matchGroup($id, $group);

        //
        // Create Player data from MatchRound Repo
        // Takes group of players
        //
        // Creates data array for each player in the match
        //
        $matchUp = array();
        foreach($groupPlayers as $key=>$groupPlayer){
            $matchUp[$key]['player'] = $groupPlayer->pivot->player_id;
            $matchUp[$key]['matchHandicap'] = round($groupPlayer->pivot->handicap ,0);
            foreach ($groupPlayer->round as $round){
                $matchUp[$key]['team'] = $round->team_id;
                $matchUp[$key]['course'] = $round->course_id;
                $matchUp[$key]['holescores'] = $round->holescores;
            }
        }

        // Change lowest handicap to ZERO and change others to reflect it
            // Create array of each players handicap
            foreach($matchUp as $key=>$match){
                $matchHandicaps[] = $match['matchHandicap'];
            }

            // Determine the lowest handicap in the group
            $lowestMatchHandicap = min($matchHandicaps);

            //handicapChange is the number of strokes to offset on the others handicap
            $handicapChange = $lowestMatchHandicap * -1;

            // Adjust all handicaps
            foreach($matchUp as $key=>$match){
                $matchUp[$key]['matchHandicap'] = $match['matchHandicap'] + $handicapChange;
            }

        // Separate group into two teams

        foreach($matchUp as $key=>$item){
            $teamIds[$key] = $item['team'];
        }

        sort($teamIds);

        //$teamIds = array_slice($teamIds, 1, -1);
        $teamIds = array_unique($teamIds);
        $teamIds = array_values($teamIds); //reset array keys

        $team1Id = $teamIds[0];
        $team2Id = $teamIds[1];

        $team1 = array();
        $team2 = array();

        foreach($matchUp as $key=>$match){
            if($match['team'] ==$team1Id ){
                $team1[] = $matchUp[$key];
            } else {
                $team2[] = $matchUp[$key];
            }
        }


        $team1Name = Team::select('name')->where('id', '=', $team1Id)->get()->toArray();
        foreach($matchUp as $key=>$match){
            if($match['team'] == $team1[0]['team']){
                $team1[] = $match;
                unset($matchUp[$key]);
            }
        }

        $team2 = $matchUp;

        $team2Name = Team::select('name')->where('id', '=', $team2Id)->get()->toArray();

            $holesData = Hole::select('handicap')->where('course_id', '=', $team1[0]['course'])->get();

            $team1Scores = $this->getTeamNetScore($team1, $holesData);
            $team2Scores = $this->getTeamNetScore($team2, $holesData);

            $team1Points = $this->calculatePoints($team1Scores, $team2Scores);
            $team2Points = $this->calculatePoints($team2Scores, $team1Scores);

            // Bonus point logic
            // Occurs after 9th hole score is added to both teams

            $team1Bonus = null;
            $team2Bonus = null;

            if($team1Scores[8] != null && $team2Scores[8] != null){
                if($team1Points > $team2Points){
                    $team1Points = $team1Points + 1;
                    $team1Bonus = 1;
                }
                if($team2Points > $team1Points){
                    $team2Points = $team2Points + 1;
                    $team2Bonus = 1;
                }
                if($team1Points == $team2Points){
                    $team1Points = $team1Points + .5;
                    $team1Bonus = .5;
                    $team2Points = $team2Points + .5;
                    $team2Bonus = .5;
                }
            }

            //Save Points in Teammatches
            Teammatch::where('match_id', '=', $id)->where('team_id', '=', $team1Id)->update(array('pointsWon' => $team1Points));
            Teammatch::where('match_id', '=', $id)->where('team_id', '=', $team2Id)->update(array('pointsWon' => $team2Points));


        //Need to determine if same amount of scores are in both
        // If not then do not return

        foreach($team1Scores as $key=>$teamScore){
            if($teamScore <= 0){
                $team1Scores[$key] = '';
            }
        }
        $team1Net = array_sum($team1Scores);


        foreach($team2Scores as $key=>$teamScore){
            if($teamScore <= 0){
                $team2Scores[$key] = '';
            }
        }
        $team2Net = array_sum($team2Scores);


        $team[0] = [
            "team" =>  $team1Name[0]['name'],
            "hole1" => $team1Scores[0],
            "hole2" => $team1Scores[1],
            "hole3" => $team1Scores[2],
            "hole4" => $team1Scores[3],
            "hole5" => $team1Scores[4],
            "hole6" => $team1Scores[5],
            "hole7" => $team1Scores[6],
            "hole8" => $team1Scores[7],
            "hole9" => $team1Scores[8],
            "bonus" => $team1Bonus,
            "points" => $team1Points,
            "netscore" => $team1Net
        ];

        $team[1] = [
            "team" =>  $team2Name[0]['name'],
            "hole1" => $team2Scores[0],
            "hole2" => $team2Scores[1],
            "hole3" => $team2Scores[2],
            "hole4" => $team2Scores[3],
            "hole5" => $team2Scores[4],
            "hole6" => $team2Scores[5],
            "hole7" => $team2Scores[6],
            "hole8" => $team2Scores[7],
            "hole9" => $team2Scores[8],
            "bonus" => $team2Bonus,
            "points" => $team2Points,
            "netscore" => $team2Net,
        ];

        $data['data'] = $team;
        return $data;
	}

    private function getTeamNetScore($team, $holesData)
    {
        foreach($team as $key=>$item){
            // Create holes array for NET
            $holes = array();
            $i = 1;
            foreach($holesData as $key=>$holeData){
                $holes[$i] = $holeData->handicap;
                $i++;
            }

            // Create stroke array - how many to take away from score
            $strokeArray = [
                1 => 0,
                2 => 0,
                3 => 0,
                4 => 0,
                5 => 0,
                6 => 0,
                7 => 0,
                8 => 0,
                9 => 0
            ];
            //Create array of strokes
            for($counter = $item['matchHandicap']; $counter > 0; $counter--){
                if($counter > 9){
                    $newCounter = $counter - 9;
                    while($newCounter > 0) {
                        $strokeArray[$newCounter] = $strokeArray[$newCounter] + 1;
                        $counter--;
                        $newCounter--;
                    }
                }
                $strokeArray[$counter] = $strokeArray[$counter] + 1;
            }
            // Plus handicaps don't hit previous loop so need its own
            //Plus handicap logic

            foreach($strokeArray as $strokeKey=>$stroke){
                $holeKey = array_search($strokeKey,$holes);
                $holes[$holeKey] = $stroke;
            }

            ///now have array of strokes to subtract
            //get array of holescores

            $holeScores = $item['holescores'];
            foreach($holeScores as $key=>$holeScore){
                //check if new score is less PlayerScores
                if(isset($teamScores[$key])){ // 2nd time in for hole

                    //set temp handi score
                    $tempScore = $holeScore['score'] - $holes[$key+1];
                    if($teamScores[$key] >= $tempScore){
                        $teamScores[$key] = $holeScore['score'] - $holes[$key+1];
                    }
                } else{ // first time in for hole
                    if($holeScore['score'] != null){
                        $teamScores[$key] = $holeScore['score'] - $holes[$key+1];
                    } else{
                        $teamScores[$key] = null;
                    }
                }
            }

        }
        return $teamScores;
    }

    private function calculatePoints($team, $opponent)
    {
        $points = 0;
        $teamTotalPoints = 0;
        $opponentTotalPoints = 0;
        foreach ($team as $key=>$score){
            if($score != null){
                if($score < $opponent[$key]){
                    $points++;
                }
                if($score == $opponent[$key]){
                    $points = $points + .5;
                }
            }

        }

        return $points;
    }

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	public function getPointsByYear($year)
    {
        $teamMatches = Teammatch::select('team_id','pointsWon')->whereYear('created_at', '=', $year)->with('team')->get();
        foreach($teamMatches as $key=>$teamMatch){
            $pointsData[$key]['name'] = $teamMatch['team']['name'];
            $pointsData[$key]['points'] =  Teammatch::select('team_id','pointsWon')
                ->where('team_id', '=', $teamMatch->team_id)
                ->whereYear('created_at', '=', $year)
                ->with('team')
                ->sum('pointsWon');

        }

        $pointsData = array_map("unserialize", array_unique(array_map("serialize", $pointsData)));
        $data['data'] = $pointsData;
        return $data;
    }


}
