<?php

namespace App\Http\Controllers;

use App\Models\Contest;
use App\Models\ContestLeaderboard;
use App\Models\ContestPrize;
use App\Models\ContestQuestion;
use App\Models\Language;
use App\Models\User;
use App\Models\UserBadge;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ContestController extends Controller
{
    public function index()
    {
        $languages = Language::where('status', 1)->get();
        return view('contest', compact('languages'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'language_id' => 'nullable|exists:languages,id',
            'name' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'description' => 'required',
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp',
            'entry' => 'required|integer|min:0',
            'top_users' => 'required|integer|min:1',
            'points' => 'required|array|min:1',
            'points.*' => 'required|integer|min:1',
            'winner' => 'required|array|min:1',
            'winner.*' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Upload image
        $image = $request->file('file');
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('images/contest'), $imageName);

        // Create contest
        $contest = new Contest();
        $contest->language_id = $request->language_id ?? 0;
        $contest->name = $request->name;
        $contest->start_date = $request->start_date;
        $contest->end_date = $request->end_date;
        $contest->description = $request->description;
        $contest->image = $imageName;
        $contest->entry = $request->entry;
        $contest->prize_status = 0;
        $contest->status = 0;
        $contest->date_created = Carbon::now();
        $contest->save();

        // Create contest prizes
        $points = array_filter($request->points);
        $winners = $request->winner;
        
        for ($i = 0; $i < count($points); $i++) {
            $prize = new ContestPrize();
            $prize->contest_id = $contest->id;
            $prize->top_winner = $winners[$i];
            $prize->points = $points[$i];
            $prize->save();
        }

        return redirect()->route('contest.index')->with('success', 'Contest created successfully');
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'language_id' => 'nullable|exists:languages,id',
            'name' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'description' => 'required',
            'update_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
            'entry' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $contest = Contest::findOrFail($id);
        
        if (config('app.language_mode')) {
            $contest->language_id = $request->language_id ?? 0;
        }
        
        $contest->name = $request->name;
        $contest->start_date = $request->start_date;
        $contest->end_date = $request->end_date;
        $contest->description = $request->description;
        $contest->entry = $request->entry;

        if ($request->hasFile('update_file')) {
            // Delete old image
            if ($contest->image && File::exists(public_path('images/contest/' . $contest->image))) {
                File::delete(public_path('images/contest/' . $contest->image));
            }
            
            $image = $request->file('update_file');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/contest'), $imageName);
            $contest->image = $imageName;
        }

        $contest->save();

        return redirect()->route('contest.index')->with('success', 'Contest updated successfully');
    }

    public function destroy($id)
    {
        $contest = Contest::findOrFail($id);
        
        // Delete prizes
        $contest->prizes()->delete();
        
        // Delete questions
        foreach ($contest->questions as $question) {
            if ($question->image && File::exists(public_path('images/contest-question/' . $question->image))) {
                File::delete(public_path('images/contest-question/' . $question->image));
            }
        }
        $contest->questions()->delete();
        
        // Delete leaderboard
        $contest->leaderboard()->delete();
        
        // Delete image
        if ($contest->image && File::exists(public_path('images/contest/' . $contest->image))) {
            File::delete(public_path('images/contest/' . $contest->image));
        }
        
        $contest->delete();
        
        return response()->json(['success' => true]);
    }

    public function updateStatus(Request $request)
    {
        $contest = Contest::findOrFail($request->update_id);
        
        // Check if contest has questions
        if ($contest->questions()->count() == 0) {
            return redirect()->back()->with('error', 'Not enough questions for active contest');
        }
        
        $contest->status = $request->status;
        $contest->save();
        
        return redirect()->route('contest.index')->with('success', 'Contest updated successfully');
    }

    public function contestPrize($id)
    {
        $contest = Contest::findOrFail($id);
        $maxWinner = ContestPrize::where('contest_id', $id)
            ->orderBy('top_winner', 'desc')
            ->first();
        
        return view('contest_prize', [
            'contest' => $contest,
            'max' => $maxWinner ? $maxWinner->top_winner : 0
        ]);
    }

    public function storeContestPrize(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contest_id' => 'required|exists:contests,id',
            'winner' => 'required|integer|min:1',
            'points' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $prize = new ContestPrize();
        $prize->contest_id = $request->contest_id;
        $prize->top_winner = $request->winner;
        $prize->points = $request->points;
        $prize->save();

        return redirect()->route('contest.prize', $request->contest_id)->with('success', 'Prize created successfully');
    }

    public function updateContestPrize(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'edit_id' => 'required|exists:contest_prizes,id',
            'points' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $prize = ContestPrize::findOrFail($request->edit_id);
        $prize->points = $request->points;
        $prize->save();

        return redirect()->route('contest.prize', $prize->contest_id)->with('success', 'Prize updated successfully');
    }

    public function destroyContestPrize($id)
    {
        $prize = ContestPrize::findOrFail($id);
        $prize->delete();
        
        return response()->json(['success' => true]);
    }

    public function contestLeaderboard($id)
    {
        $contest = Contest::findOrFail($id);
        return view('contest_leaderboard', compact('contest'));
    }

    public function contestPrizeDistribute($id)
    {
        $contest = Contest::where('end_date', '<=', Carbon::now())
            ->where('id', $id)
            ->first();
        
        if (!$contest) {
            return redirect()->route('contest.index')
                ->with('error', 'Prize distribution is currently not available. Check contest end date.');
        }
        
        if ($contest->prize_status == 1) {
            return redirect()->route('contest.index')
                ->with('error', "Prize already distributed for {$contest->name}!");
        }
        
        $prizes = ContestPrize::where('contest_id', $contest->id)
            ->orderBy('top_winner', 'asc')
            ->get();
        
        if ($prizes->isEmpty()) {
            return redirect()->route('contest.index')
                ->with('error', "Prize can not be distributed for {$contest->name}!");
        }
        
        foreach ($prizes as $prize) {
            $rank = $prize->top_winner;
            $winnerPoints = $prize->points;
            
            $winners = ContestLeaderboard::join('users', 'users.id', '=', 'contest_leaderboards.user_id')
                ->select('contest_leaderboards.*', 'users.firebase_id', 'users.coins')
                ->where('contest_id', $contest->id)
                ->orderBy('score', 'desc')
                ->get();
            
            // Get users at the specific rank
            $rankedWinners = [];
            $currentRank = 1;
            $prevScore = null;
            
            foreach ($winners as $index => $winner) {
                if ($index > 0 && $winner->score != $prevScore) {
                    $currentRank++;
                }
                
                if ($currentRank == $rank) {
                    $rankedWinners[] = $winner;
                }
                
                $prevScore = $winner->score;
            }
            
            foreach ($rankedWinners as $winner) {
                // Add tracker record
                \DB::table('trackers')->insert([
                    'user_id' => $winner->user_id,
                    'uid' => $winner->firebase_id,
                    'points' => $winnerPoints,
                    'type' => 'wonContest',
                    'status' => 0,
                    'date' => Carbon::today()->format('Y-m-d')
                ]);
                
                // Update user coins
                $newCoins = $winner->coins + $winnerPoints;
                User::where('id', $winner->user_id)->update(['coins' => $newCoins]);
                
                // Set badge
                $this->setBadges($winner->user_id, 'most_wanted_winner');
            }
        }
        
        // Update contest prize status
        $contest->prize_status = 1;
        $contest->save();
        
        return redirect()->route('contest.index')
            ->with('success', "Successfully prize distributed for {$contest->name}!");
    }

    protected function setBadges($userId, $type)
    {
        $userBadge = UserBadge::where('user_id', $userId)->first();
        $counterName = $type . '_counter';
        
        if ($userBadge) {
            if ($userBadge->$type == 0) {
                $badge = \DB::table('badges')->where('type', $type)->first();
                
                if ($badge) {
                    $counter = $badge->badge_counter;
                    $userCounter = $userBadge->$counterName;
                    $userCounter++;
                    
                    if ($userCounter < $counter) {
                        $userBadge->$counterName = $userCounter;
                        $userBadge->save();
                    } else if ($counter == $userCounter) {
                        $powerEliteCounter = $userBadge->power_elite_counter + 1;
                        $this->setPowerEliteBadge($userId, $powerEliteCounter);
                        
                        $userBadge->$counterName = $userCounter;
                        $userBadge->$type = 1;
                        $userBadge->save();
                        
                        $this->sendBadgesNotification($userId, $type);
                    }
                }
            }
        }
    }

    protected function setPowerEliteBadge($userId, $counter)
    {
        $type = 'power_elite';
        $userBadge = UserBadge::where('user_id', $userId)->first();
        $counterName = $type . '_counter';
        
        if ($userBadge) {
            if ($userBadge->$type == 0) {
                $badge = \DB::table('badges')->where('type', $type)->first();
                
                if ($badge) {
                    $badgeCounter = $badge->badge_counter;
                    
                    if ($counter < $badgeCounter) {
                        $userBadge->$counterName = $counter;
                        $userBadge->save();
                    } else if ($counter == $badgeCounter) {
                        $userBadge->$counterName = $counter;
                        $userBadge->$type = 1;
                        $userBadge->save();
                        
                        $this->sendBadgesNotification($userId, $type);
                    }
                }
            }
        }
    }

    protected function sendBadgesNotification($userId, $type)
    {
        // This would be implemented with Firebase in a real application
        // For now, we'll just log it
        \Log::info("Badge notification sent to user $userId for badge type $type");
    }
}