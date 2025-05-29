export const colors = [
    'red',
    'blue',
    'green',
    'indigo',
    'purple',
    'orange',
    'brown',
    'deep-orange',
    'blue-grey',
]

export function getRandomColor() {
    return colors[Math.floor(Math.random() * this.colors.length)]
}
