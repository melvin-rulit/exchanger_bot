<script>
import { usePusher } from '@/helpers/usePusher'
import { useSound } from '@/helpers/useSound'
import { useUserStore } from '@/stores/userStore.js'
import { eventBus } from '@/utils/eventBus.js'
import { computed } from 'vue'

export default {
  setup() {
    const { pusher } = usePusher()
    const { playSound } = useSound()
    const userStore = useUserStore()

    const notificationSettings = computed(() => {
      return (userStore.currentUser?.settings?.find(s => s.key === 'notification') || { is_active: false })
    })

    const subscribeToConsultation = () => {
      const channel = pusher.subscribe('consultation')
      channel.bind('new_message', async (data) => {
        if (notificationSettings.value.is_active){
          playSound('new_sms.mp3')
        }

        try {
          eventBus.emit('newMessage')
        } catch (e) {
          console.error('Ошибка при обновлении сообщений:', e)
        }
      })
    }

    subscribeToConsultation()

    return {userStore}
  },
  render() {
    return null // Компонент невидимый
  }
}
</script>

