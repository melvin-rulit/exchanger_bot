/**
 * Сжимает изображение до максимальных размеров (по умолчанию 640x640)
 * @param {File} file - Исходное изображение
 * @param {number} maxSize - Максимальная ширина/высота
 * @returns {Promise<File>} - Сжатое изображение в виде File
 */
export async function resizeImage(file, maxSize = 640) {
    return new Promise((resolve, reject) => {
        const img = new Image()
        const reader = new FileReader()

        reader.onload = (e) => {
            img.onload = () => {
                const canvas = document.createElement('canvas')
                const ctx = canvas.getContext('2d')

                let width = img.width
                let height = img.height

                if (width > height) {
                    if (width > maxSize) {
                        height *= maxSize / width
                        width = maxSize
                    }
                } else {
                    if (height > maxSize) {
                        width *= maxSize / height
                        height = maxSize
                    }
                }

                canvas.width = width
                canvas.height = height
                ctx.drawImage(img, 0, 0, width, height)

                canvas.toBlob((blob) => {
                    if (!blob) {
                        reject(new Error('Ошибка при преобразовании изображения.'))
                        return
                    }

                    const resizedFile = new File([blob], file.name, {
                        type: file.type,
                        lastModified: Date.now()
                    })

                    resolve(resizedFile)
                }, file.type || 'image/jpeg')
            }

            img.onerror = reject
            img.src = e.target.result
        }

        reader.onerror = reject
        reader.readAsDataURL(file)
    })
}
