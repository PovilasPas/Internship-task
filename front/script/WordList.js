import {elt} from './Utils.js'

const table = document.querySelector('#word-table')

const insertBtn = document.querySelector('#insert-word-btn')

insertBtn.addEventListener('click', (e) => {
    window.location.assign('InsertWord.html')
})

function populateWordsTable() {
    fetch('http://localhost:8000/api/words')
        .then((res) => res.json())
        .then((data) => {
            for (let word of data) {
                const updateBtn = elt(
                    'button',
                    {
                        classList: ['w-100'],
                        onclick: (e) => handleUpdateClick(e, word)
                    },
                    {},
                    'Update'
                )
                const deleteBtn = elt(
                    'button',
                    {
                        classList: ['w-100'],
                        onclick: (e) => handleDeleteClick(e, word.id)
                    }
                    , {},
                    'Delete'
                )
                const row = elt(
                    'tr',
                    {},
                    {},
                    elt('td', {}, {}, word.id),
                    elt('td', {}, {}, word.word),
                    elt('td', {}, {}, word.hyphenated),
                    elt('td', {}, {}, updateBtn),
                    elt('td', {}, {}, deleteBtn)
                )
                table.appendChild(row)
            }
        })
}

populateWordsTable()

function handleUpdateClick(e, word) {
    window.location.assign(`UpdateWord.html?id=${word.id}&word=${word.word}&hyphenated=${word.hyphenated}`)
}

function handleDeleteClick(e, wordId) {
    fetch(`http://localhost:8000/api/words/${wordId}`, {
        method: 'DELETE'
    }).then(() => {
        e.target.parentNode.parentNode.remove();
    })
}
