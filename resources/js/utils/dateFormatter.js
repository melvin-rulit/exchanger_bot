export const getFormattedDate = () => {
    const parts = new Intl.DateTimeFormat('ru-RU', {
        timeZone: 'Europe/Chisinau',
        weekday: 'long',
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    }).formatToParts(new Date())

    let dayName, day, month, year

    for (const part of parts) {
        if (part.type === 'weekday') dayName = part.value
        if (part.type === 'day') day = part.value
        if (part.type === 'month') month = part.value
        if (part.type === 'year') year = part.value
    }

    dayName = dayName.charAt(0).toUpperCase() + dayName.slice(1)

    return `${dayName} ${day} ${month} ${year}`
}
