export function createElement(tag, props, style, ...children) {
    let dom = document.createElement(tag)
    if (props) Object.assign(dom, props)
    if (style) Object.assign(dom.style, style)
    for (let child of children) {
        if (child instanceof Node) {
            dom.appendChild(child)
        } else {
            dom.appendChild(document.createTextNode(child))
        }
    }
    return dom
}
