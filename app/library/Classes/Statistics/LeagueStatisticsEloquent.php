<?php namespace GolfLeague\Statistics\League;

use \Round;
use \Player;
use \Skin;

class LeagueStatisticsEloquent implements LeagueStatistics
{
    public function topFiveLowestScores()
    {
        return Round::with('player','course')->orderBy('score')->take(5)->get();
    }
    public function topFiveLowestScoresByYear($year)
    {
        $date1 = $year . '-01-01';
        $date2 = $year . '-12-31';
        return Round::with('player', 'course')
            ->where('match_id', '>', '0')
            ->where('date', '>=', $date1)
            ->where('date', '<=', $date2)
            ->orderBy('score')
            ->take(5)
            ->get();
    }
    public function topFiveScoringAverageByYear($year)
    {
        //Get players
        //For each player where match > 0 select scores and average them
        //Store in players array
        $date1 = $year . '-01-01';
        $date2 = $year . '-12-31';
        $players = Player::all();
        $average = array();
        $i = 0;
        foreach($players as $key => $player) {
            $rounds = Round::where('match_id', '>', '0')
                ->where('player_id', '=', $player->id)
                ->where('date', '>=', $date1)
                ->where('date', '<=', $date2)
                ->get();
            $scores = array();
            foreach($rounds as $round) {
                $scores[] = $round->score;
            }
            if(count($scores) > 0) {
                $average[$i]['average'] = round((array_sum($scores) / count($scores)) ,2);
                $average[$i]['player_id'] = $player->id;
                $average[$i]['name'] = $player->name;
                $average[$i]['rounds'] = count($scores);
                $i++;
            }
        }
        array_multisort($average);
        return $average;
    }
    public function mostSkinsByYear($year)
    {
        $year = $year . '-01-01';
        $players = Player::all();
        $i = 0;
        $skinsCount = array();
        foreach($players as $key => $player) {
            $skins = Skin::with('player','level')
                ->where('player_id', '=', $player->id)
                ->where('created_at', '>', $year)
                ->get();
            if(count($skins) > 0) {
                $skinsCount[$i]['skins'] = $skins->count();
                $skinsCount[$i]['name'] = $player->name;
                $i++;
            }
        }
        array_multisort($skinsCount, SORT_DESC);
        return $skinsCount;
    }
    public function totalEagles(){}
    public function totalBirdies(){}
    public function totalPars(){}
    public function totalBogeys(){}
    public function totalDoubles(){}
    public function totalOthers(){}

}