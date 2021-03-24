export default (value) => {
    const date = new Date(value)
    return date.toLocaleDateString(['en-US'], {month: 'short', day: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit'})  //if you want, you can change locale date string
}
