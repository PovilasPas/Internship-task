<<<<<<< HEAD:front/script/WordList.js
import {createElement} from './Utils.js'
=======
import {elt} from './Utils.js'
import env from "../environment/env.js";
>>>>>>> a3735bc (dockerization):frontend/script/WordList.js

const table = document.querySelector('#word-table')

const insertButton = document.querySelector('#insert-word-btn')

insertButton.addEventListener('click', (e) => {
    window.location.assign('InsertWord.html')
})

function populateWordsTable() {
    fetch('http://localhost:8000/api/words')
    .then((res) => res.json())
    .then((data) => {
        for (let word of data) {
            const updateButton = createElement(
                'button',
                {
                    classList: ['w-100'],
                    onclick: (e) => handleUpdateClick(e, word.id)
                },
                {},
                'Update',
            )
            const deleteButton = createElement(
                'button',
                {
                    classList: ['w-100'],
                    onclick: (e) => handleDeleteClick(e, word.id)
                }
                , {},
                'Delete',
            )
            const row = createElement(
                'tr',
                {},
                {},
                createElement('td', {}, {}, word.id),
                createElement('td', {}, {}, word.word),
                createElement('td', {}, {}, word.hyphenated || ''),
                createElement('td', {}, {}, updateButton),
                createElement('td', {}, {}, deleteButton),
            )
            table.appendChild(row)
        }
    })
}

populateWordsTable()

function handleUpdateClick(e, wordId) {
    window.location.assign(`UpdateWord.html?id=${wordId}`)
}

function handleDeleteClick(e, wordId) {
    fetch(`${env.BACKEND_API}/words/${wordId}`, {
        method: 'DELETE'
    }).then(() => {
        e.target.parentNode.parentNode.remove();
    })
}
