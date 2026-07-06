@props(['name'])

@php
$icon = match($name) {
    'WiFi' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="24" height="24"><path d="M5 12.55a11 11 0 0 1 14.08 0M1.42 9a16 16 0 0 1 21.16 0M8.53 16.11a6 6 0 0 1 6.94 0"/><line x1="12" y1="20" x2="12.01" y2="20"/></svg>',
    'TV' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="24" height="24"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><polyline points="2 17 12 23 22 17"/></svg>',
    'Cuisine' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="24" height="24"><path d="M3 2v7c0 1.1.9 2 2 2h4c1.1 0 2-.9 2-2V2"/><path d="M13 2v7c0 1.1.9 2 2 2h4c1.1 0 2-.9 2-2V2"/><path d="M8 19h8"/><path d="M5 19h14v2H5z"/></svg>',
    'Parking' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="24" height="24"><circle cx="12" cy="12" r="1"/><path d="M12 2v6m0 4v6M7 12a5 5 0 0 1 10 0"/><path d="M2 12h4m12 0h4"/></svg>',
    'Climatisation' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="24" height="24"><path d="M6 5h12c1.1 0 2 .9 2 2v10c0 1.1-.9 2-2 2H6c-1.1 0-2-.9-2-2V7c0-1.1.9-2 2-2z"/><line x1="8" y1="10" x2="8" y2="16"/><line x1="12" y1="10" x2="12" y2="16"/><line x1="16" y1="10" x2="16" y2="16"/></svg>',
    'Chauffage' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="24" height="24"><path d="M6 17c0-2 1-4 3-5.5.5-.3.5-1 0-1.3C7 8 6 6 6 4c0-3 2-4 4-4s4 1 4 4c0 2-1 4-3 5.2-.5.3-.5 1 0 1.3 2 1.5 3 3.5 3 5.5 0 3-2 5-4 5s-4-2-4-5z"/></svg>',
    'Machine à laver' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="24" height="24"><rect x="2" y="2" width="20" height="20" rx="2" ry="2"/><circle cx="12" cy="12" r="7"/><circle cx="12" cy="12" r="3"/></svg>',
    'Piscine' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="24" height="24"><path d="M3 9h18v10H3V9z"/><line x1="3" y1="9" x2="2" y2="5"/><line x1="21" y1="9" x2="22" y2="5"/><path d="M7 13a2 2 0 1 1 0 4 2 2 0 0 1 0-4z"/><path d="M17 13a2 2 0 1 1 0 4 2 2 0 0 1 0-4z"/></svg>',
    'Spa' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="24" height="24"><circle cx="12" cy="8" r="2"/><path d="M12 2v2m6 4l-1.4-1.4M8.4 8.6L7 7.2M5 12h2m9 0h2M8.6 15.4l-1.4 1.4M16 15l-1.4 1.4"/><path d="M12 20a8 8 0 1 1 0-16 8 8 0 0 1 0 16z" stroke="currentColor" fill="none"/></svg>',
    'Gym' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="24" height="24"><line x1="6" y1="4" x2="6" y2="20"/><line x1="18" y1="4" x2="18" y2="20"/><circle cx="6" cy="4" r="2"/><circle cx="6" cy="20" r="2"/><circle cx="18" cy="4" r="2"/><circle cx="18" cy="20" r="2"/><line x1="9" y1="9" x2="15" y2="15"/><line x1="15" y1="9" x2="9" y2="15"/></svg>',
    'Balcon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="24" height="24"><path d="M3 5h18v12H3V5z"/><line x1="3" y1="5" x2="3" y2="2"/><line x1="21" y1="5" x2="21" y2="2"/><line x1="6" y1="17" x2="6" y2="22"/><line x1="12" y1="17" x2="12" y2="22"/><line x1="18" y1="17" x2="18" y2="22"/><line x1="6" y1="22" x2="18" y2="22"/></svg>',
    'Terrasse' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="24" height="24"><rect x="3" y="4" width="18" height="14" rx="1"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="9" y1="4" x2="9" y2="18"/><line x1="15" y1="4" x2="15" y2="18"/><path d="M3 18h18v2H3z"/></svg>',
    default => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="24" height="24"><circle cx="12" cy="12" r="10"/></svg>',
};
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center text-gray-700']) }}>
    {!! $icon !!}
</span>
