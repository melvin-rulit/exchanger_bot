import Pusher from 'pusher-js'

let pusherInstance = null

export function usePusher() {
    if (!pusherInstance) {
        pusherInstance = new Pusher(import.meta.env.VITE_PUSHER_APP_KEY, {
            cluster: 'eu',
            logToConsole: true,
        })
    }

    return {
        pusher: pusherInstance
    }
}
