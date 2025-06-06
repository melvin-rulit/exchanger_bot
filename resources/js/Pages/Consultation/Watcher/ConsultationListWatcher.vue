<script>
import { usePusher } from '@/helpers/usePusher'
import { useSound } from '@/helpers/useSound'
import { useUserStore } from '@/stores/userStore.js'
import { ConsultationService } from '@/services/ConsultationService.js'
import { useConsultationStore } from '@/stores/consultationStore'
import { eventBus } from '@/utils/eventBus.js'
import { computed } from 'vue'

export default {
  setup() {
    const { pusher } = usePusher()
    const { playSound } = useSound()
    const userStore = useUserStore()
    const consultationStore = useConsultationStore()

    const notificationSettings = computed(() => {
      return (userStore.currentUser?.settings?.find(s => s.key === 'notification') || { is_active: false })
    })

    const subscribeToConsultation = () => {
      const channel = pusher.subscribe('consultation')
      channel.bind('new_message', async (data) => {
        const response = await ConsultationService.getMessages('', 1)
        consultationStore.setMessages(response.data.data)

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

    return {userStore, consultationStore}
  },
  render() {
    return null // Компонент невидимый
  }
}
</script>

