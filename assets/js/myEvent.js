function togglePanel(panelId) {
    const target = document.getElementById(panelId);
    const card = target.closest('.event-card');
    card.querySelectorAll('.dropdown-panel').forEach(p => { if(p.id !== panelId) p.style.display = 'none'; });
    target.style.display = (target.style.display === 'block') ? 'none' : 'block';
}

function handleSum(e, id) {
    e.preventDefault();
    const f = e.target;
    
    const plants = parseFloat(f.plants.value) || 0;
    const waste = parseFloat(f.waste.value) || 0;
    const recycled = parseFloat(f.recycled.value) || 0;

    if (plants <= 0 && waste <= 0 && recycled <= 0) {
        alert("Please provide at least one data (Plants, Waste, or Recycled) greater than 0.");
        return;
    }

    const body = `eventId=${id}&plants=${plants}&waste=${waste}&recycled=${recycled}`;
    fetch("/EcoLeaf/backend/action_summary.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: body
    }).then(res => res.text()).then(data => {
        if(data.trim() === "success") { alert("Summary submitted!"); location.reload(); }
        else alert("Error: " + data);
    });
}

let rjData = null;
function openReject(eid, sid) { rjData = {eid, sid}; document.getElementById('rejectModal').style.display = 'block'; }
function closeReject() { document.getElementById('rejectModal').style.display = 'none'; }

document.getElementById('sendReject').onclick = function() {
    const sel = document.querySelector('input[name="rj"]:checked');
    if(!sel) return alert("Select a reason");
    updateStatus(rjData.eid, rjData.sid, 'rejected', sel.value);
    closeReject();
}

function updateStatus(eid, sid, status, reason = '') {
    const body = `eventId=${eid}&studentId=${sid}&status=${status}&reason=${encodeURIComponent(reason)}`;
    fetch("../backend/action_participation.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: body
    }).then(res => res.text()).then(data => {
        if(data.trim() === "success") location.reload();
        else alert("Error: " + data);
    });
}