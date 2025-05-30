<script>
import { usePusher } from '@/helpers/usePusher'
import { ConsultationService } from '@/services/ConsultationService.js'
import { useConsultationStore } from '@/stores/consultationStore'
import { useSound } from '@/helpers/useSound'
import { eventBus } from '@/utils/eventBus.js'

export default {
  setup() {
    const { pusher } = usePusher()
    const { playSound } = useSound()
    const consultationStore = useConsultationStore()

    const subscribeToConsultation = () => {
      const channel = pusher.subscribe('consultation')
      channel.bind('new_message', async (data) => {
        playSound('new_sms.mp3')
        try {
          const response = await ConsultationService.getMessages('', 1)
          consultationStore.setMessages(response.data.data)
          eventBus.emit('newMessage', response.data.data)
        } catch (e) {
          console.error('Ошибка при обновлении сообщений:', e)
        }
      })
    }

    subscribeToConsultation()
  },

  render() {
    return null // Компонент невидимый
  }
}
</script>

