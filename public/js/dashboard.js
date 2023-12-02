// Load the Visualization API and the piechart package.
google.charts.load('current', {'packages': ['corechart']});

// Set a callback to run when the Google Visualization API is loaded.
google.charts.setOnLoadCallback(drawChart);


$.ajax({
    url: '/ajax', // Symfony route
    type: 'GET',
    dataType: 'script', // Specify that the response is JavaScript
    success: function () {
        // Use the variable 'myData' in your JavaScript code
        console.log(myData);
        drawChart(myData);
    },
    error: function (error) {
        console.error('Error:', error);
    }
});

function drawChart(transactions) {
    // Assuming transactions is an array of objects with properties amount, type, date, income, expense

    // Create a DataTable
    var dataTable = new google.visualization.DataTable();
    dataTable.addColumn('string', 'Date');
    dataTable.addColumn('number', 'Amount');
    if (!Array.isArray(transactions)) {
        console.error('Transactions data is not an array or is undefined.');
        return;
    }
    // Iterate over transactions and add rows to the DataTable
    transactions.forEach(function (transaction) {
        var date = new Date(transaction.date);
        var formattedDate = date.toLocaleDateString(); // You can format the date as needed
        var amount = parseFloat(transaction.amount);

        dataTable.addRow([formattedDate, amount]);
    });

    // Set options for the chart
    var options = {
        title: 'Transaction Amounts Over Time',
        hAxis: {
            title: 'Date'
        },
        vAxis: {
            title: 'Amount'
        }
    };

    // Create and draw the chart
    var chart = new google.visualization.ColumnChart(document.getElementById('chartContainer'));
    chart.draw(dataTable, options);
}

