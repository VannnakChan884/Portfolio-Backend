<?php
function renderDisabledAction($text, $tooltip, $icon = null, $isButton = true)
{
    $html = '<div class="relative group inline-block">';
    
    if ($isButton) {
        $html .= '<button disabled class="bg-gray-400 text-white px-4 py-2 rounded opacity-50 cursor-not-allowed flex items-center gap-2">';
        if ($icon) {
            $html .= "<i class='$icon'></i>";
        }
        $html .= htmlspecialchars($text) . '</button>';
    } else {
        $html .= '<a href="#" class="pointer-events-none text-gray-400 underline cursor-not-allowed flex items-center gap-1">';
        if ($icon) {
            $html .= "<i class='$icon'></i>";
        }
        $html .= htmlspecialchars($text) . '</a>';
    }

    // Tooltip
    $html .= '<div class="absolute -top-10 left-1/2 -translate-x-1/2 whitespace-nowrap
                bg-black text-white text-xs rounded px-2 py-1
                opacity-0 group-hover:opacity-100 transition
                pointer-events-none z-10">
                ' . htmlspecialchars($tooltip) . '
              </div>';
    
    $html .= '</div>';

    echo $html;
}
?>
