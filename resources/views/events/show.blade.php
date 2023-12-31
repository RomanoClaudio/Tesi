<x-app-layout>
    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="backdrop-blur-180 overflow-hidden shadow-md sm:rounded-lg">
                <x-group-menu :group="$group" />
                <div class="p-6 backdrop-blur-180">
                    <div class="mb-1 -mt-1 flex justify-between items-center">
                        @switch($event->status)
                            @case(\App\Enum\EventStatus::PLANNED)
                                <h2 class="text-xl text-stone-100 font-semibold">In programma il <span class="text-amber-300">{{ $event->date->format('d/m/Y') }}</span> alle <span class="text-amber-300">{{ $event->date->format('H:i') }}</span></h2>
                                @break
                            @case(\App\Enum\EventStatus::ACTIVE)
                                <h2 class="text-xl text-stone-100 font-semibold"><span class="text-emerald-300">Attivo</span> dalle <span class="text-emerald-300">{{ $event->date->format('H:i') }}</span></h2>
                                @break
                            @case(\App\Enum\EventStatus::COMPLETED)
                                <h2 class="text-xl text-stone-100 font-semibold"><span class="text-indigo-300">Concluso</span> il <span class="text-indigo-300">{{ $event->date->format('d/m/Y') }}</span></h2>
                                @break
                            @case(\App\Enum\EventStatus::CANCELED)
                                <h2 class="text-xl text-stone-100 font-semibold"><span class="text-red-300">Evento cancellato</span></h2>
                                @break
                        @endswitch
                        <p class="text-stone-400 text-xs">Creato il {{ $event->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <h2 class="text-2xl text-stone-100 font-semibold">{{ $event->title }}</h2>
                    <p class="text-stone-300 text-justify">{{ $event->description }}</p>

                    <div class="mt-4 flex justify-between items-center">
                        <p class="text-sm text-stone-400 -mb-1">Pianificato da {{ $event->user->name }} in
                            <a href="{{ route('groups.show', ['group' => $group]) }}" class="underline text-stone-200 hover:text-stone-300">{{ $event->group->name }}</a>
                        </p>
                        <div class="flex justify-between items-center">
                            @if (auth()->user()->isAdmin() && $event->status->isPlanned())
                                <form action="{{ route('events.cancel', ['group' => $group, 'event' => $event]) }}" method="POST">
                                    @csrf
                                    <x-danger-button type="submit" onclick="return confirm('Sei sicuro di voler annullare questo evento?')">
                                        <!-- <x-feathericon-trash-2 /> -->
                                        Cancella
                                    </x-danger-button>
                                </form>
                            @elseif (auth()->user()->isAdmin() && $event->status->isCanceled())
                                <form action="{{ route('events.destroy', ['group' => $group, 'event' => $event]) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <x-danger-button type="submit" onclick="return confirm('Sei sicuro di voler eliminare definitivamente questo evento?')">
                                        <!-- <x-feathericon-trash-2 /> -->
                                        Elimina definitivamente
                                    </x-danger-button>
                                </form>
                            @elseif (auth()->user()->isAdmin() && $event->status->isCompleted())
                                <form action="{{ route('events.destroy', ['group' => $group, 'event' => $event]) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <x-danger-button type="submit" onclick="return confirm('Sei sicuro di voler eliminare definitivamente questo evento?')">
                                        <!-- <x-feathericon-trash-2 /> -->
                                        Elimina definitivamente
                                    </x-danger-button>
                                </form>
                            @elseif (auth()->user()->isAdmin() && $event->status->isActive())
                                <form action="{{ route('events.end', ['group' => $group, 'event' => $event]) }}" method="POST">
                                    @csrf
                                    <x-danger-button type="submit" onclick="return confirm('Sei sicuro di voler terminare questo evento?')">
                                        <!-- <x-feathericon-trash-2 /> -->
                                        Termina
                                    </x-danger-button>
                                </form>
                            @endif
                            @if ($event->status->isActive())
                                <form id="lobby__form" class="ml-2">
                                    <div class="form__field__wrapper">
                                        <input type="hidden" name="name" value="{{auth()->user()->name}}" />
                                    </div>
                                    <div class="form__field__wrapper">
                                        <input type="hidden" name="room" value="{{$event->id}}"/>
                                    </div>
                                    <div id="greenDot"></div>
                                    <x-primary-button type="submit">Vai alla stanza</x-primary-button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<style>
    #greenDot {
        width: 20px;
        height: 20px;
        background-color: green;
        border-radius: 50%; /* Per ottenere una forma circolare */
        display: none; /* Inizialmente nascosto */
    }
</style>

<script src={{asset("agora-rtm-sdk-1.5.1.js")}}></script>

<script>
    var groupId = {{ $group->id }};
    var eventId = {{ $event->id }};
    let form = document.getElementById('lobby__form')
    let displayname = sessionStorage.getItem('display_name')
    if (displayname){
        form.name.value = displayname
    }
    form.addEventListener('submit',(e) => {
        e.preventDefault()
        sessionStorage.setItem('display_name', e.target.name.value)
        let invitecode = e.target.room.value
        if (!invitecode){
            invitecode= String(Math.floor(Math.random() * 10000))
        }
        window.location.href =  `/groups/${groupId}/events/${eventId}/room?room=${invitecode}`})
</script>

<script>
    async function check(){
        let token =null
        const App_ID="c4e01afaa134412b85a0be9679574954"
        rtmClient =  AgoraRTM.createInstance(App_ID)
        let uid=String(Math.floor(Math.random() * 10000))
        await rtmClient.login({uid,token})
        await rtmClient.addOrUpdateLocalUserAttributes({'name':'{{auth()->user()->name}}'})

        channel =  await rtmClient.createChannel('{{$group->name}}')
        await channel.join()
        let members =  await channel.getMembers()
        console.log('membri',members.length)
        await channel.leave()
        await rtmClient.logout()
        if(members >= 1){
            document.getElementById("greenDot").style.display = "none";

        } else{
            document.getElementById("greenDot").style.display = "block";
        }
    }
    check()
</script>
