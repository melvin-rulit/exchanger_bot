export const range = (start, end) => {
    if (end < start) return []
    return Array.from({ length: end - start + 1 }, (_, i) => start + i)
}
