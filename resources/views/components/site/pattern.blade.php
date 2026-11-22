@props(['type' => 'api', 'label' => 'Abstract illustration'])

{{--
    Abstract technical placeholder for projects without a screenshot (or when
    a screenshot fails to load). Deliberately not a fake screenshot.
--}}
@php
    $width = 600;
    $height = 400;
    // One tiled <pattern> draws the 40px grid instead of 26 separate lines.
    $gridId = 'pattern-grid-'.\Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(8));
    $grid = '<defs><pattern id="'.$gridId.'" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M40 0H0V40" fill="none" class="p-line" stroke-width="1"/></pattern></defs>'
        .'<rect width="'.$width.'" height="'.$height.'" fill="url(#'.$gridId.')"/>';

    $shapes = '';

    switch ($type) {
        case 'topology':
            $points = [[300, 90], [160, 200], [440, 200], [210, 320], [390, 320], [300, 210]];
            foreach ([[5, 0], [5, 1], [5, 2], [5, 3], [5, 4], [1, 3], [2, 4]] as [$from, $to]) {
                $shapes .= '<line class="p-acc-s" x1="'.$points[$from][0].'" y1="'.$points[$from][1].'" x2="'.$points[$to][0].'" y2="'.$points[$to][1].'" stroke-width="1" opacity=".45"/>';
            }
            foreach ($points as $index => [$cx, $cy]) {
                $radius = $index === 5 ? 26 : 13;
                $shapes .= '<circle cx="'.$cx.'" cy="'.$cy.'" r="'.($radius + 10).'" class="p-acc-f"/>';
                $shapes .= '<circle cx="'.$cx.'" cy="'.$cy.'" r="'.$radius.'" class="'.($index === 5 ? 'p-acc' : 'p-mid').'"/>';
            }
            break;

        case 'chat':
            $rows = [[40, 60, 240, 'p-mid'], [280, 120, 280, 'p-acc-f'], [40, 190, 200, 'p-mid'], [320, 250, 240, 'p-acc-f'], [40, 320, 260, 'p-mid']];
            foreach ($rows as [$rx, $ry, $rw, $class]) {
                $shapes .= '<rect x="'.$rx.'" y="'.$ry.'" width="'.$rw.'" height="44" rx="12" class="'.$class.'"/>';
            }
            foreach ($rows as [$rx, $ry, $rw]) {
                $shapes .= '<rect x="'.($rx + 16).'" y="'.($ry + 18).'" width="'.($rw * 0.55).'" height="8" rx="4" class="p-soft"/>';
            }
            $shapes .= '<circle cx="548" cy="56" r="6" class="p-acc"/>';
            break;

        case 'dashboard':
            $shapes .= '<rect x="24" y="24" width="120" height="352" rx="10" class="p-soft"/>';
            for ($i = 0; $i < 6; $i++) {
                $shapes .= '<rect x="40" y="'.(52 + $i * 34).'" width="'.(60 + ($i % 3) * 14).'" height="8" rx="4" class="'.($i === 1 ? 'p-acc' : 'p-mid').'"/>';
            }
            foreach ([0, 1, 2] as $k) {
                $shapes .= '<rect x="'.(164 + $k * 140).'" y="24" width="128" height="72" rx="10" class="p-soft"/>';
            }
            foreach ([120, 180, 90, 210, 150, 240, 170, 200] as $k => $bar) {
                $shapes .= '<rect x="'.(176 + $k * 48).'" y="'.(370 - $bar).'" width="28" height="'.$bar.'" rx="4" class="'.($k === 5 ? 'p-acc' : 'p-mid').'"/>';
            }
            break;

        case 'doc':
            $shapes .= '<rect x="150" y="34" width="300" height="340" rx="12" class="p-soft"/>';
            for ($j = 0; $j < 9; $j++) {
                $shapes .= '<rect x="178" y="'.(70 + $j * 30).'" width="'.($j % 3 === 0 ? 150 : 240).'" height="8" rx="4" class="'.($j === 3 || $j === 4 ? 'p-acc-f' : 'p-mid').'"/>';
            }
            $shapes .= '<rect x="170" y="152" width="262" height="62" rx="6" fill="none" class="p-acc-s" stroke-width="1.5"/>';
            break;

        case 'routes':
            foreach (['GET', 'POST', 'GET', 'PUT', 'DELETE'] as $k => $verb) {
                $top = 50 + $k * 64;
                $shapes .= '<rect x="40" y="'.$top.'" width="520" height="44" rx="8" class="p-soft"/>';
                $shapes .= '<text x="58" y="'.($top + 27).'" class="p-text"'.($k === 1 ? ' style="fill:var(--color-accent)"' : '').'>'.$verb.'</text>';
                $shapes .= '<rect x="140" y="'.($top + 18).'" width="'.(140 + ($k * 37) % 160).'" height="8" rx="4" class="p-mid"/>';
            }
            break;

        default:
            foreach ([180, 260, 220, 140, 300, 200, 240, 120, 210] as $k => $length) {
                $shapes .= '<rect x="'.($k > 0 && $k < 8 ? 64 : 40).'" y="'.(48 + $k * 34).'" width="'.$length.'" height="9" rx="4" class="'.($k === 2 || $k === 5 ? 'p-acc-f' : 'p-mid').'"/>';
            }
            $shapes .= '<rect x="380" y="60" width="180" height="260" rx="12" class="p-soft"/>';
            $shapes .= '<text class="p-text" x="400" y="92">200 OK</text>';
            $shapes .= '<rect x="400" y="110" width="120" height="8" rx="4" class="p-acc"/>';
    }
@endphp

<svg {{ $attributes->class('pattern') }} viewBox="0 0 {{ $width }} {{ $height }}" preserveAspectRatio="xMidYMid slice" role="img" aria-label="{{ $label }}">{!! $grid.$shapes !!}</svg>
