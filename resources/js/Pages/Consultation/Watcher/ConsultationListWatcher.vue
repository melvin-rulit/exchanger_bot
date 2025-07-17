<script>
import { usePusher } from '@/helpers/usePusher'
import { useSound } from '@/helpers/useSound'
import { useUserStore } from '@/stores/userStore.js'
import { ConsultationService } from '@/services/ConsultationService.js'
import { useConsultationStore } from '@/stores/consultationStore'
import { eventBus } from '@/utils/eventBus.js'
import { computed, onUnmounted } from 'vue'

export default {
  setup() {
    const { pusher } = usePusher()
    const { playSound } = useSound()
    const userStore         = useUserStore()
    const consultationStore = useConsultationStore()

    const notificationSettings = computed(() => {
      return userStore.currentUser?.settings?.find(s => s.key === 'notification')
        || { is_active: false }
    })

    const channelName = 'consultation'
    let channel = pusher.channel(channelName)
    if (!channel) {
      channel = pusher.subscribe(channelName)
    }
    channel.unbind('new_message')

    const handler = async (data) => {
      const response = await ConsultationService.getMessages('', 1)
      consultationStore.setMessages(response.data.data)

      if (notificationSettings.value.is_active) {
        playSound('new_sms.mp3')
      }

      eventBus.emit('newMessage')
    }

    channel.bind('new_message', handler)

    onUnmounted(() => {
      channel.unbind('new_message', handler)
    })

    return {}
  },
  render() {
    return null
  }
}
</script>
