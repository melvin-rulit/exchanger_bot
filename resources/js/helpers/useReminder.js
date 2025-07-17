import { ref, onMounted } from 'vue'
import { useSound} from '@/helpers/useSound'
const { playSound, stopSoundById } = useSound()

const STORAGE_KEY = 'order-reminders'
const attentionOrders = ref([])
const soundInstances = new Map()


export function useReminder() {
    // const remindManager = (orderId) => {
    //     if (!attentionOrders.value.includes(orderId)) {
    //         attentionOrders.value.push(orderId)
    //         const soundId = playSound('alarm.ogg')
    //         soundInstances.set(orderId, soundId)
    //     }
    // }

    const remindManager = (orderId) => {
        console.log('remindManager called with orderId:', orderId)
        if (!attentionOrders.value.includes(orderId)) {
            attentionOrders.value.push(orderId)
            console.log('attentionOrders updated:', attentionOrders.value)
            const soundId = playSound('tikanie-budilnika.ogg')
            soundInstances.set(orderId, soundId)
        }
    }

    const saveReminders = (reminders) => {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(reminders))
    }

    const loadReminders = () => {
        const raw = localStorage.getItem(STORAGE_KEY)
        return raw ? JSON.parse(raw) : []
    }

    const setReminder = (orderId, delayMs) => {
        const remindAt = Date.now() + delayMs
        const reminders = loadReminders().filter(r => r.orderId !== orderId)
        reminders.push({ orderId, remindAt })
        saveReminders(reminders)

        scheduleReminder({ orderId, remindAt })
    }

    const scheduleReminder = (reminder) => {
        const timeLeft = reminder.remindAt - Date.now()
        if (timeLeft <= 0) {
            removeReminder(reminder.orderId)
            return
        }
        setTimeout(() => {
            remindManager(reminder.orderId)
        }, timeLeft)
    }

    const removeReminder = (orderId) => {
        const reminders = loadReminders().filter(r => r.orderId !== orderId)
        saveReminders(reminders)

        const index = attentionOrders.value.indexOf(orderId)
        if (index !== -1) {
            attentionOrders.value.splice(index, 1)
        }
        if (soundInstances.has(orderId)) {
            const soundId = soundInstances.get(orderId)
            stopSoundById(soundId)
            soundInstances.delete(orderId)
        }
    }

    const restoreReminders = () => {
        const reminders = loadReminders()
        reminders.forEach(scheduleReminder)
    }

    onMounted(() => {
        restoreReminders()
    })

    return {
        setReminder,
        removeReminder,
        attentionOrders,
    }
}
