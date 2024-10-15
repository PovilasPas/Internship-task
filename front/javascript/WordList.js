const container = document.getElementById('word-table-container')

function listWords() {
    let response
    fetch('http://localhost:8000/api/words')
        .then((res) => res.json())
        .then((data) => {
            const table = document.createElement('table')

            const header = document.createElement('tr')
            table.appendChild(header)

            const idHeader = document.createElement('th')
            idHeader.textContent = 'id'
            const wordHeader = document.createElement('th')
            wordHeader.textContent = 'word'
            const hyphenatedHeader = document.createElement('th')
            hyphenatedHeader.textContent = 'hyphenated'
            header.appendChild(idHeader)
            header.appendChild(wordHeader)
            header.appendChild(hyphenatedHeader)

            container.appendChild(table);
            for (word of data) {

            }
        })
}

listWords()
