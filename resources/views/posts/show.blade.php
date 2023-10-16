<x-app-layout>
    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <x-group-menu :group="$group" />
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-1 -mt-1 flex justify-between items-center">
                        <h2 class="text-2xl font-semibold">{{ $post->title }}</h2>
                        <p class="text-gray-600 text-xs">{{ $post->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <p class="text-gray-700">{{ $post->content }}</p>
                    <div class="mt-4">
                        <p class="text-sm text-gray-400 -mb-1">Scritto da {{ $post->user->name }} in
                            <a href="{{ route('groups.show', ['group' => $group]) }}" class="underline text-gray-500 hover:text-gray-800">{{ $post->group->name }}</a>
                        </p>
                    </div>
                </div>
            </div>


            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg px-6 pb-6 mt-6">
                <h3 class="text-2xl font-semibold mb-4 my-4">Commenti</h3>
                <h3 class="text-lg font-semibold">Aggiungi un Commento</h3>
                <form method="POST" action="{{ route('comments.store', ['group' => $group, 'post' => $post]) }}">
                    @csrf
                    <textarea name="content" id="content" rows="4" class="w-full p-2 border rounded border-gray-300 focus:outline-none focus:border-indigo-500 focus:ring-indigo-500" placeholder="Inserisci il tuo commento" required></textarea>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded shadow-md text-sm uppercase">
                        Aggiungi Commento
                    </button>
                </form>
                @foreach($comments as $comment)
                        <x-comment :comment="$comment" />
                @endforeach
            </div>

        </div>
    </div>
</x-app-layout>

