document.addEventListener("DOMContentLoaded", () => {
    // pobranie wszystkich tabel z klasą main-table
    const tables = document.querySelectorAll(".main-table");

    tables.forEach(table => {
        const headers = table.querySelectorAll("th");
        const tbody = table.querySelector("tbody") || table;

        headers.forEach((header, index) => {
            const text = header.textContent.trim();
            // pominięcie kolumn porządkowych (#) oraz akcji (Edit/Delete)
            if (text === "#" || text === "Action") return;

            header.style.cursor = "pointer";
            header.style.userSelect = "none";
            
            let asc = true; // kierunek sortowania

            header.addEventListener("click", () => {
                // omijamy wiersze nagłówkowe (które zawierają th) aby się nie sortowały
                let rows = Array.from(tbody.querySelectorAll("tr")).filter(row => !row.querySelector("th"));

                // sortowanie wierszy
                rows.sort((rowA, rowB) => {
                    const cellA = rowA.children[index].textContent.trim();
                    const cellB = rowB.children[index].textContent.trim();

                    // sprawdzanie czy to liczby
                    const numA = parseFloat(cellA);
                    const numB = parseFloat(cellB);
                    if (!isNaN(numA) && !isNaN(numB)) {
                        return asc ? numA - numB : numB - numA;
                    }

                    // sprawdzanie czy to daty
                    const dateA = Date.parse(cellA);
                    const dateB = Date.parse(cellB);
                    if (!isNaN(dateA) && !isNaN(dateB)) {
                        return asc ? dateA - dateB : dateB - dateA;
                    }

                    // sortowanie alfabetyczne
                    return asc 
                        ? cellA.localeCompare(cellB, undefined, { numeric: true, sensitivity: 'base' })
                        : cellB.localeCompare(cellA, undefined, { numeric: true, sensitivity: 'base' });
                });

                // usuwanie starych strzałek
                headers.forEach(h => {
                    const arrow = h.querySelector(".sort-arrow");
                    if (arrow) arrow.remove();
                });
                
                // dodanie nowej strzałki z kolorem neonowym
                const arrow = document.createElement("span");
                arrow.className = "sort-arrow";
                arrow.innerHTML = asc ? " ▲" : " ▼";
                arrow.style.color = "#ff4488";
                header.appendChild(arrow);

                // wstawienie posortowanych wierszy z powrotem
                rows.forEach(row => tbody.appendChild(row));

                asc = !asc; // odwrócenie kierunku
            });
        });
    });
});
