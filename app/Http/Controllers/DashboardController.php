<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Contest;
use App\Models\Question;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'count_category' => Category::where('type', 1)->count(),
            'count_subcategory' => DB::table('subcategories')
                ->join('categories', 'categories.id', '=', 'subcategories.maincat_id')
                ->where('categories.type', 1)
                ->count(),
            'count_question' => Question::count(),
            'count_user' => User::count(),
            'count_live_contest' => Contest::where('start_date', '<=', Carbon::today())
                ->where('end_date', '>', Carbon::today())
                ->count(),
        ];

        // Get years for user registrations
        $yearData = User::selectRaw('DISTINCT YEAR(created_at) as year')
            ->get()
            ->pluck('year')
            ->toArray();
        $data['years'] = $yearData;

        // Get monthly data for current year
        $monthData = DB::table('users')
            ->selectRaw('MONTHNAME(created_at) as month_name, COUNT(id) as user_count')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month_name')
            ->get();
        $data['month_data'] = $monthData;

        // Get weekly data for current month
        $weekData = DB::table('users')
            ->selectRaw('DAYNAME(created_at) as day_name, COUNT(id) as user_count')
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->groupBy('day_name')
            ->get();
        $data['week_data'] = $weekData;

        // Get daily data for current month
        $dayData = DB::table('users')
            ->selectRaw('DAY(created_at) as day_name, COUNT(id) as user_count')
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->groupBy('day_name')
            ->get();
        $data['day_data'] = $dayData;

        return view('dashboard', $data);
    }

    public function getYearForMonthChart($year)
    {
        $monthData = DB::table('users')
            ->selectRaw('MONTHNAME(created_at) as month_name, COUNT(id) as user_count')
            ->whereYear('created_at', $year)
            ->groupBy('month_name')
            ->get();

        $mLabel = $mData = [];
        foreach ($monthData as $mD) {
            $mLabel[] = $mD->month_name;
            $mData[] = $mD->user_count ?? 0;
        }

        $maxMonthData = !empty($mData) ? max($mData) : 0;
        $stepSizeMonth = $maxMonthData > 10 ? round($maxMonthData / 10) : 1;

        $data = [
            'mName' => $mLabel,
            'mD' => $mData,
            'stepSizeMonth' => $stepSizeMonth
        ];

        return response()->json($data);
    }

    public function users()
    {
        return view('users');
    }

    public function battleStatistics($id)
    {
        $generalStat = DB::table('user_statistics')
            ->select('user_statistics.*', 'users.name', 'users.profile', 'c1.category_name as strong_category', 'c2.category_name as weak_category')
            ->leftJoin('users', 'users.id', '=', 'user_statistics.user_id')
            ->leftJoin('categories as c1', 'c1.id', '=', 'user_statistics.strong_category')
            ->leftJoin('categories as c2', 'c2.id', '=', 'user_statistics.weak_category')
            ->where('user_statistics.user_id', $id)
            ->get();

        $battleStat = DB::select(
            "SELECT 
                (SELECT COUNT(`winner_id`) FROM battle_statistics WHERE winner_id = ?) AS Victories,
                (SELECT COUNT(*) FROM (SELECT DISTINCT `date_created` from battle_statistics WHERE (user_id1 = ? || user_id2 = ?) AND is_drawn = 1) as d) AS Drawn,
                (SELECT COUNT(`winner_id`) FROM battle_statistics WHERE (user_id1 = ? || user_id2 = ?) AND winner_id != ? and is_drawn = 0) AS Loose,
                (SELECT name FROM users WHERE id = ?) AS name",
            [$id, $id, $id, $id, $id, $id, $id]
        );

        return view('battle_statistics', [
            'general_stat' => $generalStat,
            'battle_stat' => $battleStat
        ]);
    }
}