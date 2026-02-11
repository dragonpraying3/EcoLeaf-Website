// Carbon Calculator Script
// Reads inputs, updates breakdown and totals
// Generates simple advice based on highest impacts
// Defensive checks for invalid/empty inputs
document.addEventListener('DOMContentLoaded', function () {
    // Mapping of input emissionCategory to whether they are positive (emissions) or negative (savings)
    const emissionCategory = {
        fuel: { pos: true },
        transport: { pos: true },
        electric: { pos: true },
        waste: { pos: true },
        recycling: { pos: false },
        cycling_walking: { pos: false }
    };

    // Format value with sign and unit
    //if positive, prefix with '+', else with '-'
    function formatEmissionValue(val, pos) {
        const s = Number(val).toFixed(2) + ' kg CO₂';
        return (pos ? '+' : '-') + s;
    }

    // Set class for positive/negative values
    //adds class 'pos' for positive values and 'neg' for negative values for styling
    function setSignClass(el, pos) {
        el.classList.remove('pos', 'neg');
        el.classList.add(pos ? 'pos' : 'neg');
    }

    //  Update calculations and display results
    function update() {
        let valid = true;
        Object.keys(emissionCategory).forEach(k => {
            const raw = document.getElementById(k).value;
            if (raw === "" || isNaN(parseFloat(raw))) { // Check for valid number
                valid = false;
            }
        });

        if (!valid) { // Reset display if invalid input
            document.getElementById('totalEmissions').textContent = "0.00 kg CO₂";
            document.getElementById('co2Saved').textContent = "0.00 kg CO₂";
            document.getElementById('netFootprint').textContent = "0.00 kg CO₂";
            document.getElementById('treesEq').textContent = "Equivalent to 0 trees needed";
            document.getElementById('adviceList').innerHTML = "";
            return;  // Exit early
        }

        let positives = 0;
        let savings = 0;
        // Calculate totals and update display
        Object.keys(emissionCategory).forEach(k => {
            const v = parseFloat(document.getElementById(k).value) || 0;

            const pos = emissionCategory[k].pos;
            const valueElelment = document.getElementById(k + 'Val');
            if (valueElelment) {
                valueElelment.textContent = v.toFixed(0);
            }
            let breakdownElemnt = document.getElementById('bd' + k.charAt(0).toUpperCase() + k.slice(1));
            if (!breakdownElemnt && k === 'electric')
                breakdownElemnt = document.getElementById('breakdownElemntectric');
            if (!breakdownElemnt && k === 'cycling_walking')
                breakdownElemnt = document.getElementById('bdCycling_walking');
            if (breakdownElemnt) {
                b
                reakdownElemnt.textContent = formatEmissionValue(v, pos).replace(' kg CO₂', ' kg');
                setSignClass(breakdownElemnt, pos);
            }
            if (pos)
                positives += v;
            else savings += v;
        });

        // Compute net footprint
        const net = positives - savings;
        document.getElementById('totalEmissions').textContent = positives.toFixed(2) + ' kg CO₂';
        document.getElementById('co2Saved').textContent = savings.toFixed(2) + ' kg CO₂';
        document.getElementById('netFootprint').textContent = net.toFixed(2) + ' kg CO₂';
        const trees = (net / 10).toFixed(1);
        document.getElementById('treesEq').textContent = 'Equivalent to ' + trees + ' trees needed';

        // Generate personalized advice
        const fuel = parseFloat(document.getElementById('fuel').value) || 0;
        const transport = parseFloat(document.getElementById('transport').value) || 0;
        const electricity = parseFloat(document.getElementById('electric').value) || 0;
        const waste = parseFloat(document.getElementById('waste').value) || 0;
        const recycling = parseFloat(document.getElementById('recycling').value) || 0;
        const cyclingWalking = parseFloat(document.getElementById('cycling_walking').value) || 0;

        // Identify category with highest emissions
        const maxCategory = ['Fuel', fuel, 'fuel'];
        if (transport > maxCategory[1]) { maxCategory[0] = 'Transport'; maxCategory[1] = transport; maxCategory[2] = 'transport'; }
        if (electricity > maxCategory[1]) { maxCategory[0] = 'Electricity'; maxCategory[1] = electricity; maxCategory[2] = 'electricity'; }
        if (waste > maxCategory[1]) { maxCategory[0] = 'Waste'; maxCategory[1] = waste; maxCategory[2] = 'waste'; }

        // Compile advice based on inputs
        const advice = [];
        if (net > 50) {
            advice.push('Focus on ' + maxCat[0] + ' to reduce footprint by 20%.');
        }
        if (transport >= 20) {
            advice.push('Use public transport, carpool, or reduce trips.');
        }
        if (electricity >= 20) {
            advice.push('Switch to LED lighting and improve appliance efficiency.');
        }
        if (fuel >= 20) {
            advice.push('Drive less, maintain vehicle efficiency, consider cleaner fuels.');
        }
        if (waste >= 10) {
            advice.push('Reduce waste, reuse items, and compost organics.');
        }
        if (recycling < 5) {
            advice.push('Increase recycling of paper, plastic, metal, and glass.');
        }
        if (cyclingWalking < 5) {
            advice.push('Add short walking or cycling trips to your routine.');
        }
        if (advice.length === 0) {
            advice.push('Great balance. Keep reinforcing low-impact habits.');
        }

        // Display advice
        const list = document.getElementById('adviceList');
        list.innerHTML = '';
        advice.forEach(a => {
            const li = document.createElement('li');
            li.textContent = a;
            list.appendChild(li);
        });
    }

    const btn = document.getElementById('calcBtn');
    if (btn) btn.addEventListener('click', update);
});