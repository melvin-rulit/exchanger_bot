export function useSound() {
    // Map: ключ — имя файла, значение — массив аудио объектов
    const audios = new Map()
    // Map: ключ — ID экземпляра, значение — аудио объект
    const instances = new Map()
    let idCounter = 0

    const playSound = (file) => {
        const audio = new Audio(`/audio/${file}`)
        audio.play().catch(err => console.error('Ошибка воспроизведения:', err))

        if (!audios.has(file)) {
            audios.set(file, [])
        }
        audios.get(file).push(audio)

        const id = ++idCounter
        instances.set(id, { file, audio })

        audio.onended = () => {

            const arr = audios.get(file)
            if (arr) {
                const index = arr.indexOf(audio)
                if (index !== -1) arr.splice(index, 1)
                if (arr.length === 0) audios.delete(file)
            }
            instances.delete(id)
        }

        return id
    }

    const stopSoundById = (id) => {
        const instance = instances.get(id)
        if (!instance) return
        const { file, audio } = instance

        audio.pause()
        audio.currentTime = 0

        const arr = audios.get(file)
        if (arr) {
            const index = arr.indexOf(audio)
            if (index !== -1) arr.splice(index, 1)
            if (arr.length === 0) audios.delete(file)
        }
        instances.delete(id)
    }

    const stopSoundByFile = (file) => {
        if (!audios.has(file)) return
        const arr = audios.get(file)
        arr.forEach(audio => {
            audio.pause()
            audio.currentTime = 0
        })
        audios.delete(file)

        for (const [id, instance] of instances.entries()) {
            if (instance.file === file) {
                instances.delete(id)
            }
        }
    }

    return {
        playSound,
        stopSoundById,
        stopSoundByFile,
    }
}
