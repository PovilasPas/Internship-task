const form = document.querySelector('#word-update-form')

const wordInput = form.querySelector('input[name=word]')

const hyphenatedInput = form.querySelector('input[name=hyphenated]')

const urlParams = new URLSearchParams(window.location.search)

form.addEventListener('submit', (e) => {
    e.preventDefault()
    const id = urlParams.get('id')
    const word = wordInput.value
    const hyphenated = hyphenatedInput.value
    const data = {
        word,
        hyphenated
    }
    const json = JSON.stringify(data)

    fetch(`http://localhost:8000/api/words/${id}`, {
        method: 'PUT',
        body: json
    })
        .then((res) => res.text())
        .then((data) => {
            window.location.assign('WordList.html')
        })
})

function populateWordForm() {
    const word = urlParams.get('word')
    const hyphenated = urlParams.get('hyphenated')
    wordInput.value = word
    hyphenatedInput.value = hyphenated
}

populateWordForm()
