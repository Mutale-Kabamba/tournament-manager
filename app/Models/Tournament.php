<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class Tournament extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'title',
        'admin_pin',
        'state',
    ];

    protected $casts = [
        'state' => 'array',
    ];

    /**
     * Get or create the active tournament.
     */
    public static function active(): self
    {
        try {
            if (!Schema::hasTable('tournaments')) {
                Artisan::call('migrate', ['--force' => true]);
            }

            return static::firstOrCreate(
                ['slug' => 'default'],
                [
                    'title' => 'Boys & Girls Knockout Cup',
                    'admin_pin' => '1234',
                    'state' => static::defaultState(),
                ]
            );
        } catch (\Throwable $e) {
            Log::warning('Tournament::active() fallback: ' . $e->getMessage());

            $fallback = new static([
                'slug' => 'default',
                'title' => 'Boys & Girls Knockout Cup',
                'admin_pin' => '1234',
                'state' => static::defaultState(),
            ]);
            $fallback->exists = false;

            return $fallback;
        }
    }

    /**
     * Generate the default tournament state matching the handwritten sheet.
     */
    public static function defaultState(): array
    {
        $boysTeams = [
            ['id' => 'b1', 'name' => 'Anthony', 'group' => 'G1', 'seed' => 1],
            ['id' => 'b2', 'name' => 'Mumba', 'group' => 'G1', 'seed' => 2],
            ['id' => 'b3', 'name' => 'Frank', 'group' => 'G1', 'seed' => 3],
            ['id' => 'b4', 'name' => 'Eliza', 'group' => 'G1', 'seed' => 4],
            ['id' => 'b5', 'name' => 'Poniso', 'group' => 'G1', 'seed' => 5],
            ['id' => 'b6', 'name' => 'Betty', 'group' => 'G2', 'seed' => 6],
            ['id' => 'b7', 'name' => 'Tasila', 'group' => 'G2', 'seed' => 7],
            ['id' => 'b8', 'name' => 'Kambenja', 'group' => 'G2', 'seed' => 8],
            ['id' => 'b9', 'name' => 'Wisdom', 'group' => 'G2', 'seed' => 9],
            ['id' => 'b10', 'name' => 'Thelma', 'group' => 'G2', 'seed' => 10],
        ];

        $girlsTeams = [
            ['id' => 'g1', 'name' => 'Eugene', 'group' => 'G1', 'seed' => 1],
            ['id' => 'g2', 'name' => 'Gift', 'group' => 'G1', 'seed' => 2],
            ['id' => 'g3', 'name' => 'Edma', 'group' => 'G1', 'seed' => 3],
            ['id' => 'g4', 'name' => 'Gracious', 'group' => 'G1', 'seed' => 4],
            ['id' => 'g5', 'name' => 'Fredrick', 'group' => 'G1', 'seed' => 5],
            ['id' => 'g6', 'name' => 'Maureen', 'group' => 'G1', 'seed' => 6],
        ];

        $boysG1Ids = array_column(array_filter($boysTeams, fn ($t) => $t['group'] === 'G1'), 'id');
        $boysG2Ids = array_column(array_filter($boysTeams, fn ($t) => $t['group'] === 'G2'), 'id');
        $girlsIds = array_column($girlsTeams, 'id');

        return [
            'adminPin' => '1234',
            'boysTeams' => $boysTeams,
            'girlsTeams' => $girlsTeams,
            'boysMatches' => array_merge(
                static::generateTwoGamesPerTeamFixtures($boysG1Ids, 'boys', 'G1'),
                static::generateTwoGamesPerTeamFixtures($boysG2Ids, 'boys', 'G2')
            ),
            'girlsMatches' => static::generateTwoGamesPerTeamFixtures($girlsIds, 'girls', 'G1'),
            'boysKnockout' => [
                'sf1' => ['id' => 'b_sf1', 'label' => 'Semi-Final 1', 'sublabel' => '1st Group 1 vs 2nd Group 2', 'homeScore' => null, 'awayScore' => null, 'homePen' => null, 'awayPen' => null, 'status' => 'upcoming', 'customHomeId' => null, 'customAwayId' => null],
                'sf2' => ['id' => 'b_sf2', 'label' => 'Semi-Final 2', 'sublabel' => '1st Group 2 vs 2nd Group 1', 'homeScore' => null, 'awayScore' => null, 'homePen' => null, 'awayPen' => null, 'status' => 'upcoming', 'customHomeId' => null, 'customAwayId' => null],
                'third' => ['id' => 'b_3rd', 'label' => '3rd Place Playoff', 'sublabel' => 'Loser SF1 vs Loser SF2', 'homeScore' => null, 'awayScore' => null, 'homePen' => null, 'awayPen' => null, 'status' => 'upcoming', 'customHomeId' => null, 'customAwayId' => null],
                'final' => ['id' => 'b_fin', 'label' => 'Boys Grand Final', 'sublabel' => 'Winner SF1 vs Winner SF2', 'homeScore' => null, 'awayScore' => null, 'homePen' => null, 'awayPen' => null, 'status' => 'upcoming', 'customHomeId' => null, 'customAwayId' => null],
            ],
            'girlsKnockout' => [
                'sf1' => ['id' => 'g_sf1', 'label' => 'Semi-Final 1', 'sublabel' => '1st Place vs 4th Place', 'homeScore' => null, 'awayScore' => null, 'homePen' => null, 'awayPen' => null, 'status' => 'upcoming', 'customHomeId' => null, 'customAwayId' => null],
                'sf2' => ['id' => 'g_sf2', 'label' => 'Semi-Final 2', 'sublabel' => '2nd Place vs 3rd Place', 'homeScore' => null, 'awayScore' => null, 'homePen' => null, 'awayPen' => null, 'status' => 'upcoming', 'customHomeId' => null, 'customAwayId' => null],
                'third' => ['id' => 'g_3rd', 'label' => '3rd Place Playoff', 'sublabel' => 'Loser SF1 vs Loser SF2', 'homeScore' => null, 'awayScore' => null, 'homePen' => null, 'awayPen' => null, 'status' => 'upcoming', 'customHomeId' => null, 'customAwayId' => null],
                'final' => ['id' => 'g_fin', 'label' => 'Girls Grand Final', 'sublabel' => 'Winner SF1 vs Winner SF2', 'homeScore' => null, 'awayScore' => null, 'homePen' => null, 'awayPen' => null, 'status' => 'upcoming', 'customHomeId' => null, 'customAwayId' => null],
            ],
        ];
    }

    /**
     * Balanced 2-Games-Per-Team schedule generator.
     */
    public static function generateTwoGamesPerTeamFixtures(array $teamIds, string $division, string $group): array
    {
        $n = count($teamIds);
        if ($n < 3) return [];

        $rawPairs = [];
        for ($i = 0; $i < $n; $i++) {
            $rawPairs[] = [
                'homeId' => $teamIds[$i],
                'awayId' => $teamIds[($i + 1) % $n],
            ];
        }

        $orderedPairs = [];
        for ($i = 0; $i < $n; $i += 2) {
            $orderedPairs[] = $rawPairs[$i];
        }
        for ($i = 1; $i < $n; $i += 2) {
            $orderedPairs[] = $rawPairs[$i];
        }

        $matches = [];
        foreach ($orderedPairs as $idx => $pair) {
            $matches[] = [
                'id' => "{$division}_{$group}_m" . ($idx + 1) . '_' . time() . '_' . substr(bin2hex(random_bytes(3)), 0, 4),
                'division' => $division,
                'group' => $group,
                'matchNumber' => $idx + 1,
                'homeId' => $pair['homeId'],
                'awayId' => $pair['awayId'],
                'homeScore' => null,
                'awayScore' => null,
                'status' => 'upcoming',
            ];
        }

        return $matches;
    }
}
