const scheduleRaw = [
  { start: "06:15", end: "06:35", description: "起床" },
  { start: "06:45", end: "09:00", description: "朝シロフク神業 カフェ日誌" },
  { start: "09:05", end: "09:20", description: "トースト" },
  { start: "10:15", end: "10:26", description: "帰宅" },
  { start: "11:05", end: "11:06", description: "富士ソフトのPC返送準備" },
  { start: "11:48", end: "11:50", description: "ヤマト運輸でノートPC配送" },
  { start: "11:50", end: "12:19", description: "支払い" },
  { start: "12:35", end: "13:51", description: "昼まかない、ゆにわマート" },
  { start: "14:30", end: "15:58", description: "メビウス管理人ミー" },
  { start: "19:08", end: "20:45", description: "パティ締め掃除 北極流日誌" },
  { start: "25:52", end: "25:52", description: "たまには提供される側の気持ちも思い出して。" },
  { start: "26:13", end: "26:35", description: "ふろ" },
  { start: "26:37", end: "26:52", description: "ブログちょっと書いた" }
];

function timeToMinutes(t) {
  let [h, m] = t.split(":").map(Number);
  return h * 60 + m;
}

const pxPerMinute = 0.4;
const timelineStartHour = 4;
let timelineEndHour = 26;

function drawTimeline(targetEle, scheduleRawData){

  // 終了時刻の最大値を取得
  const maxEndMinutes = Math.max(...scheduleRawData.map(e => timeToMinutes(e.end)));
  const maxEndHour = Math.ceil(maxEndMinutes / 60);
  if (maxEndHour > timelineEndHour) {
    timelineEndHour = maxEndHour + 0.5; // 30分追加
  }

  // タイムラインの高さを設定
  targetEle.style.height = `${(timelineEndHour - timelineStartHour) * 60 * pxPerMinute}px`;

  // 目盛り（6時、12時、18時、24時）
  const hourMarks = [6, 9, 12, 15, 18, 21, 24];
  hourMarks.forEach(hour => {
    const mark = document.createElement("div");
    mark.className = "timeline-hour-mark";
    mark.style.top = `${(hour - timelineStartHour) * 60 * pxPerMinute}px`;
    const markLabel = document.createElement("div");
    markLabel.className = "timeline-hour-mark-label";
    markLabel.textContent = `${String(hour).padStart(2, ' ')}`;
    mark.appendChild(markLabel);
    targetEle.appendChild(mark);
  });

  let lastEnd = timeToMinutes(`${timelineStartHour}:00`);

  scheduleRawData.forEach(event => {
    const start = timeToMinutes(event.start);
    const end = timeToMinutes(event.end);
    const duration = end - start;

    // 空白時間
    if (start > lastEnd) {
      const gap = start - lastEnd;
      const gapDiv = document.createElement("div");
      gapDiv.className = "timeline-gap";
      gapDiv.style.height = `${gap * pxPerMinute}px`;
      targetEle.appendChild(gapDiv);
    }

    // イベント表示
    const wrapper = document.createElement("div");
    wrapper.className = "timeline-event-wrapper";
    wrapper.style.height = `${duration * pxPerMinute}px`;

    const bar = document.createElement("div");
    bar.className = "timeline-event-bar";
    bar.style.height = "100%";

    const desc = document.createElement("div");
    desc.className = "timeline-event-desc";
    desc.innerHTML = "・" + event.description;

    const timeHover = document.createElement("div");
    timeHover.className = "timeline-hover-time";
    timeHover.textContent = `${event.start}～${event.end}`;

    wrapper.appendChild(bar);
    wrapper.appendChild(desc);
    wrapper.appendChild(timeHover);
    targetEle.appendChild(wrapper);

    lastEnd = end;
  });
}

function noteTitleToEvent(titleEle){
    const [d, t] = titleEle.textContent.trim().split("　");
    if (t && d && d.includes(" - ")) {
        const [start, end] = d.split(" - ");
        const event = {
            start: start.trim(),
            end: end.trim(),
            description: titleEle.innerHTML.replace(d + "　", "").trim()
        };
        return event;
    }
    return null;
}

window.addEventListener("load", () => {
    const calender = document.querySelectorAll(".note-content");
    [...calender].reverse().forEach((c) => {
        const list = c.querySelectorAll("li");
        const events = [];
        list.forEach((li) => {
            const event = noteTitleToEvent(li);
            if(event){
                events.push(event);
            }
        });
        if(events.length > 0){
            const timelineDiv = document.createElement("div");
            c.classList.add("timeline");
            c.innerHTML = "";
            drawTimeline(c, events);
        }
    });
});
