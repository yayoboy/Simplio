<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- SEO Meta Tags --}}
    <title>{{ $page->meta_title ?: $page->title }} - {{ $site->name }}</title>
    <meta name="description" content="{{ $page->meta_description ?: $site->description }}">

    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $page->meta_title ?: $page->title }}">
    <meta property="og:description" content="{{ $page->meta_description ?: $site->description }}">

    {{-- Twitter --}}
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:title" content="{{ $page->meta_title ?: $page->title }}">
    <meta property="twitter:description" content="{{ $page->meta_description ?: $site->description }}">

    {{-- Tailwind CSS CDN for styling --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Theme Custom CSS --}}
    @if($themeCss)
    <style>
        {!! $themeCss !!}
    </style>
    @endif

    {{-- Additional Styles --}}
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        .block-container {
            width: 100%;
        }

        /* Prose styles for text blocks */
        .prose {
            max-width: none;
        }

        .prose p {
            margin-bottom: 1em;
        }

        .prose p:last-child {
            margin-bottom: 0;
        }

        .prose strong {
            font-weight: 600;
        }

        .prose em {
            font-style: italic;
        }

        .prose a {
            color: #3b82f6;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <main>
        @foreach($blocks as $block)
            <div class="block-container">
                @include('public.blocks.' . $block->type, ['block' => $block])
            </div>
        @endforeach
    </main>
</body>
</html>
