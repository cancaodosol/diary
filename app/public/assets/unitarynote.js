class unitaryNoteService {

    getUnits = async function(date, mode) {
        const url = Routing.generate('api_get_unitary', { date: date, mode: mode });
        console.log(url);
        const res = await fetch(url, {
                headers: {
                    "Content-Type": "application/json;charset=utf-8",
                    "Accept": "application/json"
                },
            });
        const retjson = await res.json();
        return retjson;
    };

    getUnitsByTagName = async function(tagName) {
        const url = Routing.generate('api_get_unitary_by_tag', { tagName: tagName });
        console.log(url);
        const res = await fetch(url, {
                headers: {
                    "Content-Type": "application/json;charset=utf-8",
                    "Accept": "application/json"
                },
            });
        const retjson = await res.json();
        return retjson;
    };
}

class unitaryNote {
    displayNormalNotes = async function(targetEle, units) {

        units["units"].forEach((unit) => {
            let titleHtml = `<div class="unitary-date text-center">%%dateWithYoubi%%`
                        +`[<a class="btn btn-link btn-sm" href="${Routing.generate('new_unitary_with_compact', { date: unit.date })}">`
                        +`    追加W`
                        +`</a>`
                        +`<a class="btn btn-link btn-sm" href="${Routing.generate('app_unitary_date', { date: unit.date })}">`
                        +`    見る`
                        +`</a>`
                        +`]`
                    + `</div>`;
            titleHtml = titleHtml.replaceAll("%%dateWithYoubi%%", unit.dateWithYoubi);

            let notesHtml = ``;
            unit.notes.forEach((note) => {
                let tagsHtml = '';
                note.tags.forEach((tag) => {
                    let tagHtml = `<a class="badge bg-primary note-tags" href="${Routing.generate('app_unitary', { tagName: tag.name })}">`
                        + tag.name
                        +`</a>`
                    tagsHtml += tagHtml;
                });
                let noteHtml = `<div id="note-${ note.id }" note-id="${ note.id }">`
                            + `<div id="notes-today" class="unitary-title">`
                            +`  ・<a class="text-dark" href="${Routing.generate('edit_unitary_with_compact', { id: note.id })}">`
                            +`      note_title`
                            +`  </a>`
                            + tagsHtml
                            +`</div>`
                            +`<div class="unitary-text" onclick="startEditNote(${ note.id })">note_textHtml</div>`
                            +`<div class="note-edit-area" style="display: none;">`
                            +`  <textarea class="form-control" style="field-sizing: content; font-size:0.8rem;">note_textRaw</textarea>`
                            +`  <div class="mt-1 d-flex justify-content-end gap-2">`
                            +`    <button class="btn btn-sm btn-outline-secondary" type="button" onclick="closeNote(${note.id})">閉じる</button>`
                            +`    <button class="btn btn-sm btn-outline-primary" type="button" onclick="updateNote(${note.id})">更新</button>`
                            +`  </div>`
                            +`</div>`
                            +`<div class="unitary-footer"></div>`
                            +`</div>`;
                noteHtml = noteHtml.replaceAll("note_title", note.title);
                noteHtml = noteHtml.replaceAll("note_textHtml", note.textHtml);
                noteHtml = noteHtml.replaceAll("note_textRaw", note.text ?? '');
                notesHtml += noteHtml;
            });
            notesHtml += `</div>`;
            notesHtml += `<div class="btn-bar text-center mt-3 mb-5"></div>`;
            notesHtml = notesHtml.replaceAll("today", unit.date);

            targetEle.innerHTML += titleHtml;
            targetEle.innerHTML += notesHtml;
        });
    }

    displayCompactNotes = async function(targetEle, units) {

        units.forEach((unit) => {
            let titleHtml = `<div class="unitary-date text-center">%%dateWithYoubi%%`
                        +`[<a class="btn btn-link btn-sm" href="${Routing.generate('new_unitary_with_compact', { date: unit.date })}">`
                        +`    追加W`
                        +`</a>`
                        +`<a class="btn btn-link btn-sm" href="${Routing.generate('app_unitary_date', { date: unit.date })}">`
                        +`    見る`
                        +`</a>`
                        +`]`
                    + `</div>`
                    + `<div class="text-center">%%title%%</div>`;
            titleHtml = titleHtml.replaceAll("%%dateWithYoubi%%", unit.dateWithYoubi);
            titleHtml = titleHtml.replaceAll("%%title%%", unit.title);

            let notesHtml = `<hr>`
                        +`<div id="notes-today" class="unitary-text">`;
            
            unit.notes.forEach((note) => {
                let noteHtml = `<a class="note-title" href="${Routing.generate('edit_unitary_with_compact', { id: note.id })}">`
                            +`  note_title`
                            +`</a>`;
                noteHtml = noteHtml.replaceAll("note_title", note.title);
                let tagsHtml = '';
                note.tags.forEach((tag) => {
                    let tagHtml = `<a class="badge bg-primary note-tags" href="${Routing.generate('app_unitary', { tagName: tag.name })}">`
                        + tag.name
                        +`</a>`
                    tagsHtml += tagHtml;
                });
                notesHtml += `<span class="note-title-tags">${noteHtml}${tagsHtml}</span><br/>`;
            });
            notesHtml += `</div><hr>`;
            notesHtml = notesHtml.replaceAll("today", unit.date);

            targetEle.innerHTML += titleHtml;
            targetEle.innerHTML += notesHtml;
        });

        const boxes = document.querySelectorAll('.unitary-text');
        boxes.forEach((box) => {
            const events = [];
            const list = box.querySelectorAll('.note-title-tags');
            list.forEach((item, index) => {
                const ll = document.createElement('div');
                ll.appendChild(item);
                const event = noteTitleToEvent(ll);
                if(event){
                    events.push(event);
                }
            });
            if(events.length > 0){
                box.classList.add("timeline");
                box.innerHTML = "";
                drawTimeline(box, events);
            }
        });
    }

    displayCompactNotesByTag = async function(targetEle, units) {
        units.forEach((unit) => {
            let notesHtml =
                `<div class="unit-date-compact">${ unit.dateWithYoubi }</div>`
                +`<div id="notes-today" class="unitary-text">`;
            
            unit.notes.forEach((note) => {
                let noteHtml =
                    `・<a class="note-title" href="${Routing.generate('edit_unitary_with_compact', { id: note.id })}">`
                    +`  note_title`
                    +`</a>`;
                noteHtml = noteHtml.replaceAll("note_title", note.title);
                let tagsHtml = '';
                note.tags.forEach((tag) => {
                    if(tag.name == tagName) return;
                    let tagHtml = `<a class="badge bg-primary note-tags" href="${Routing.generate('app_unitary', { tagName: tag.name })}">`
                        + tag.name
                        +`</a>`
                    tagsHtml += tagHtml;
                });
                notesHtml += noteHtml + tagsHtml + '<br/>';
            });
            notesHtml += `</div><hr>`;
            notesHtml = notesHtml.replaceAll("today", unit.date);

            targetEle.innerHTML += notesHtml;
        });
    }

    displayNormalNotesByTag = async function(targetEle, units) {
        let notesHtml = ``;
        units.forEach((unit) => {
            notesHtml += `<div class="unit-date">${ unit.dateWithYoubi }</div><hr>`;
            unit.notes.forEach((note) => {
                let tagsHtml = '';
                note.tags.forEach((tag) => {
                    // if(tag.name == tagName) return;
                    let tagHtml = `<a class="badge bg-primary note-tags" href="${Routing.generate('app_unitary', { tagName: tag.name })}">`
                        + tag.name
                        +`</a>`
                    tagsHtml += tagHtml;
                });
                let noteHtml = 
                    `<div id="note-${ note.id }" note-id="${ note.id }">`
                    +`<div class="note-datetime">note_datetime</div>`
                    +`<div id="notes-today" class="unitary-title">`
                    +`  <a class="text-dark" href="${Routing.generate('edit_unitary_with_compact', { id: note.id })}">`
                    +`      note_title`
                    +`  </a>`
                    + tagsHtml
                    +`</div>`
                    +`<div class="unitary-text" onclick="startEditNote(${ note.id })">note_textHtml</div>`
                    +`<div class="note-edit-area" style="display: none;">`
                    +`  <textarea class="form-control" style="field-sizing: content; font-size:0.8rem;">note_textRaw</textarea>`
                    +`  <div class="mt-1 d-flex justify-content-end gap-2">`
                    +`    <button class="btn btn-sm btn-outline-secondary" type="button" onclick="closeNote(${note.id})">閉じる</button>`
                    +`    <button class="btn btn-sm btn-outline-primary" type="button" onclick="updateNote(${note.id})">更新</button>`
                    +`  </div>`
                    +`</div>`
                    +`<div class="unitary-footer"></div>`;
                    +`</div>`;
                noteHtml = noteHtml.replaceAll("note_datetime", note.title.substring(0, 13));
                noteHtml = noteHtml.replaceAll("note_title", note.title.substring(14, note.title.length));
                noteHtml = noteHtml.replaceAll("note_textHtml", note.textHtml);
                noteHtml = noteHtml.replaceAll("note_textRaw", note.text ?? '');
                notesHtml += noteHtml;
            });
        });

        targetEle.innerHTML += notesHtml;
    }
}

startEditNote = async function(noteId) {
    const editArea = document.querySelector(`#note-${noteId} .note-edit-area`);
    const showArea = document.querySelector(`#note-${noteId} .unitary-text`);
    editArea.style.display = "block";
    showArea.style.display = "none";
}

closeNote = async function(noteId) {
    const editArea = document.querySelector(`#note-${noteId} .note-edit-area`);
    const showArea = document.querySelector(`#note-${noteId} .unitary-text`);
    editArea.style.display = "none";
    showArea.style.display = "block";
}

updateNote = async function(noteId) {
    const editArea = document.querySelector(`#note-${noteId} .note-edit-area`);
    const editTextArea = document.querySelector(`#note-${noteId} .note-edit-area textarea`);
    const showArea = document.querySelector(`#note-${noteId} .unitary-text`);

    const payload = { text: editTextArea.value };

    try {
        let url = `${Routing.generate('api_edit_unitary', { noteId: noteId })}`;
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        if (!res.ok) throw new Error(`${res.status} ${res.statusText}`);
        const data = await res.json();

        showArea.innerHTML = data.note.textHtml;
        editArea.style.display = "none";
        showArea.style.display = "block";

    } catch (err) {
        console.error('送信エラー', err);
        alert('送信失敗: ' + err.message);
    }
}