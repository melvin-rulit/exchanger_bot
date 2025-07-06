<script>
import { usePusher } from '@/helpers/usePusher'
import { useSound } from '@/helpers/useSound'
import { useUserStore } from '@/stores/userStore.js'
import { useOrdersStore } from '@/stores/ordersStore'
import { eventBus } from '@/utils/eventBus.js'
import { computed, onUnmounted } from 'vue'

export default {
  setup() {
    const { pusher } = usePusher()
    const { playSound } = useSound()
    const userStore = useUserStore()
    const orderStore = useOrdersStore()

    const notificationSettings = computed(() => {
      return userStore.currentUser?.settings?.find(s => s.key === 'notification') || { is_active: false }
    })

    // --- New Order Channel ---
    const newOrderChannel = pusher.channel('new_order') || pusher.subscribe('new_order')
    newOrderChannel.unbind('new_order')

    const handleNewOrder = (data) => {
      orderStore.addOrder(data.order)

      if (notificationSettings.value.is_active) {
        playSound('new_order.wav')
      }
      eventBus.emit('newOrder', data.order)
    }

    newOrderChannel.bind('new_order', handleNewOrder)

    // --- Send Message Channel ---
    const messageChannel = pusher.channel('send_message') || pusher.subscribe('send_message')
    messageChannel.unbind('send_message')

    const handleNewMessage = (data) => {
      if (notificationSettings.value.is_active) {
        playSound('new_sms.mp3')
      }

      eventBus.emit('newOrderMessage', data.order)
    }

    messageChannel.bind('send_message', handleNewMessage)

    onUnmounted(() => {
      newOrderChannel.unbind('new_order', handleNewOrder)
      messageChannel.unbind('send_message', handleNewMessage)
    })

    return {}
  },
  render() {
    return null
  }
}
</script>
