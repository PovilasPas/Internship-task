const form = document.querySelector('#word-insert-form')

form.addEventListener('submit', (e) => {
    e.preventDefault()
    const formData = new FormData(form)
    const data = {}
    formData.forEach((value, key) => data[key] = value)
    const json = JSON.stringify(data)
    fetch('http://localhost:8000/api/words', {
            method: 'POST',
            body: json,
    })
    .then((res) => res.json())
    .then((data) => {
        window.location.assign('WordList.html')
    })
})
