<?php
    // session_start(); // Start the session (important for checking session variables)
    include '../admin.php';
    $admin = new Admin();
    // $admin->handleRedirect(); // Call handleRedirect to check login status and redirect

    // Removing this causes website to proceed to index.php despite isLoggedIn from admin.php returning false
    if(!$admin->isLoggedIn()) {
        header("Location: ../login.php");
        exit();
    }
    if($admin->isLoggedIn() && $admin->session_role == 'user') {
        header("Location: ../index.php");
        exit();
    }
    
    $data = $admin->getTenantCountByApartment();
    // print_r($data);

    // echo "<br><br>";
    
    $incomedata = $admin->getMonthlyIncome();
    // print_r($incomedata);

    // echo "<br><br>";

    $userRolesData = $admin->getUserRolePercentage();
    // print_r($userRolesData);

    // echo "<br><br>";

    $incomeexpenses = $admin->getIncomeExpensesData();
    // print_r($incomeexpenses);

    // echo "<br><br>";

    $incomeexpensesfiltered = $admin->getIncomeExpensesDataFiltered();
    // print_r($incomeexpensesfiltered);

    // echo "<br><br>";

    $yearlyIncomeData = $admin->getYearlyIncomeData(); // Fetch the yearly income data for the current year
    // print_r($yearlyIncomeData);

    // echo "<br><br>";
    $expenseperhouseData = $admin->getExpensesPerApartmentData();
    // print_r($expenseperhouseData);


    // Set the title for this page
    $pageTitle = "RentTrackPro"; // Change this according to the current page
    $page = "admindashboard";
?>

    <div class="container-fluid">
        <div class="row">
        <?php include 'includes/header.php'; ?>
            <div class="col main content">
                <div class="card-body bg-transparent" style="margin-top: 0; padding: 12px;">
                    <div class="row">
                        <div class="col-xl-6 py-md-2 mb-3">
                            <div class="card h-100" style="width: 100%;">
                                <div class="card-header p-3" style="background-color: #527853; color: white;">
                                    <p class="fs-4 fw-bolder text-center text-uppercase mb-0">Tenants Per Apartment</p>
                                </div>
                                <div class="card-body mt-2 position-relative">
                                    <canvas id="myChart" style="min-height: 250px; max-height: 100%;"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 py-md-2 mb-3">
                            <div class="card h-100" style="width: 100%;">
                                <div class="card-header p-3" style="background-color: #527853; color: white;">
                                    <p class="fs-4 fw-bolder text-center text-uppercase mb-0">Income Per Month</p>
                                </div>
                                <div class="card-body mt-2 position-relative">
                                    <div class="mb-3">
                                        <select id="yearDropdown" class="form-select w-auto mw-100">
                                            <option value="" disabled selected>Select Year</option>
                                        </select>
                                    </div>
                                    <canvas id="incomeChart" style="max-height: 450px;"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 py-md-2 mb-3">
                            <div class="card h-100" style="width: 100%;">
                                <div class="card-header p-3" style="background-color: #527853; color: white;">
                                    <p class="fs-4 fw-bolder text-center text-uppercase mb-0">Admin and User Count</p>
                                </div>
                                <div class="card-body mt-2 position-relative">
                                    <canvas id="roleChart" style="max-height: 100%;"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 py-md-2 mb-3">
                            <div class="card h-100" style="width: 100%;">
                                <div class="card-header p-3" style="background-color: #527853; color: white;">
                                    <p class="fs-4 fw-bolder text-center text-uppercase mb-0">Profit and Losses</p>
                                </div>
                                <div class="card-body mt-2 position-relative">
                                    <!-- Dropdown to switch views -->
                                    <!-- <div class="mb-3">
                                        <select id="chartViewSelect" class="form-select w-auto mw-100">
                                            <option value="incomeExpenses">Show Income and Expenses</option>
                                            <option value="expensesPerApartment">Show Expenses per Apartment</option>
                                        </select>
                                    </div> -->
                                    <div class="mb-3">
                                        <select id="houseSelect" class="form-select w-auto mw-100">
                                            <option value="">All Houses</option>
                                            <!-- Options will be dynamically populated by JavaScript -->
                                        </select>
                                    </div>
                                    <canvas id="incomeExpenseChart" style="max-height: 100%;"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-8 py-md-2 mx-auto mb-3">
                            <div class="card h-100" style="width: 100%;">
                                <div class="card-header p-3" style="background-color: #527853; color: white;">
                                    <p class="fs-4 fw-bolder text-center text-uppercase mb-0">Income per Year</p>
                                </div>
                                <div class="card-body mt-2 position-relative">
                                    <!-- <p class="card-text">Yearly Income Chart</p> -->
                                    <div class="mb-3 mx-auto w-75">
                                        <select id="yearlyincomeSelect" class="form-select w-auto mw-100">
                                            <option value="">All Years</option>
                                            <!-- Options will be dynamically populated by JavaScript -->
                                        </select>
                                    </div>
                                    <canvas id="yearlyIncomeChart" class="mx-auto w-75" style="max-height: 450px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                             
                <!-- <p>Home</p> -->
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

    <script>
        var ctx = document.getElementById('myChart').getContext('2d');
        var chartData = <?php echo $data; ?>;

        var labels = chartData.map(function(e) {
            return e.house_name;
        });
        var data = chartData.map(function(e) {
            return e.tenant_count;
        });

        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Tenant Count',
                    data: data,
                    backgroundColor: '#F9E8D9',
                    borderColor: '#F28543',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>

    <script>
        var ctx = document.getElementById('incomeChart').getContext('2d');
        var chartData = <?php echo $incomedata; ?>;

        // Filter the data to include only approved payments
        var filteredData = chartData.filter(function(e) {
            return e.approval === 'true';
        });

        // Extract unique years from the data
        var years = [...new Set(filteredData.map(function(e) {
            return e.month.split('-')[0]; // Extract the year from the 'month' string
        }))];

        // Populate the dropdown with the years
        var yearDropdown = document.getElementById('yearDropdown');
        years.forEach(function(year) {
            var option = document.createElement('option');
            option.value = year;
            option.text = year;
            yearDropdown.appendChild(option);
        });

        // Initialize the chart with empty data
        var incomeChart = new Chart(ctx, {
            type: 'line', // Change this to 'bar' if you prefer bar charts
            data: {
                labels: [],
                datasets: [
                    {
                        label: 'Monthly Income',
                        data: [],
                        backgroundColor: '#F9E8D9',
                        borderColor: '#F28543',
                        borderWidth: 1,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Function to update the chart based on the selected year
        function updateChart(selectedYear) {
            // Filter data for the selected year
            var filteredByYear = filteredData.filter(function(e) {
                return e.month.split('-')[0] === selectedYear;
            });

            // Extract months and income data for the selected year
            var labels = filteredByYear.map(function(e) {
                return e.month.split('-')[1]; // Extract the month
            });
            var data = filteredByYear.map(function(e) {
                return e.total_income;
            });

            // Update the chart data
            incomeChart.data.labels = labels;
            incomeChart.data.datasets[0].data = data;
            incomeChart.update();
        }

        // Handle year selection from the dropdown
        yearDropdown.addEventListener('change', function() {
            var selectedYear = this.value;
            updateChart(selectedYear);
        });

        // Automatically select the first year and display its data
        if (years.length > 0) {
            yearDropdown.value = years[0];
            updateChart(years[0]);
        }
    </script>



    <script>
        var ctx = document.getElementById('roleChart').getContext('2d');
        var chartData = <?php echo $userRolesData; ?>;

        var labels = chartData.map(function(e) {
            return e.role === 'admin' ? 'Admin' : 'User';
        });
        var data = chartData.map(function(e) {
            return e.count;
        });
        var percentages = chartData.map(function(e) {
            return e.percentage + '%';
        });

        var roleChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Role Distribution',
                        data: data,
                        backgroundColor: [
                            '#8DA58D', // Blue for Admin
                            '#F9E8D9'  // Red for User
                        ],
                        borderColor: [
                            '#527853',
                            '#F28543'
                        ],
                        hoverOffset: 40,
                        borderWidth: 3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    datalabels: {
                        color: '#000000',
                        formatter: function(value, context) {
                            return context.chart.data.labels[context.dataIndex] + '\n' + percentages[context.dataIndex];
                        },
                        font: {
                            weight: 'bold',
                            size: 16
                        }
                    },
                    legend: {
                        position: 'top',
                    }
                }
            },
            plugins: [ChartDataLabels]
        });
    </script>

    <script>
        // Initialize the chart with default data
        var ctx = document.getElementById('incomeExpenseChart').getContext('2d');
        var initialData = <?php echo ($incomeexpenses); ?>;

        // Populate house names in the dropdown using both house_id and house_name as unique combinations
        var houseSelect = document.getElementById('houseSelect');
        var uniqueHouses = new Map();

        initialData.forEach(function(e) {
            if (e.house_id && e.house_name) {
                uniqueHouses.set(e.house_id, e.house_name);
            }
        });

        // Populate dropdown options based on unique house_id and house_name pairs
        uniqueHouses.forEach(function(name, id) {
            var option = document.createElement('option');
            option.value = id;
            option.textContent = name;
            houseSelect.appendChild(option);
        });

        // Chart initialization
        var chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: initialData.map(e => e.month),
                datasets: [
                    {
                        label: 'Income',
                        data: initialData.map(e => e.total_income),
                        backgroundColor: '#8DA58D',
                        borderColor: '#527853',
                        borderWidth: 1
                    },
                    {
                        label: 'Expenses',
                        data: initialData.map(e => e.total_expenses),
                        backgroundColor: '#F9E8D9',
                        borderColor: '#F28543',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Event listener for house-specific filtering
        houseSelect.addEventListener('change', function() {
            var selectedHouseId = this.value;

            var filteredData;
            if (selectedHouseId === '') {
                // Show data for all houses
                filteredData = initialData;
            } else {
                // Filter data by selected house_id
                filteredData = initialData.filter(function(e) {
                    return e.house_id == selectedHouseId;
                });
            }

            // Update the chart with filtered data
            chart.data.labels = filteredData.map(e => e.month);
            chart.data.datasets[0].data = filteredData.map(e => e.total_income);
            chart.data.datasets[1].data = filteredData.map(e => e.total_expenses);

            chart.update(); // Refresh the chart with new data
        });



        // var ctx = document.getElementById('incomeExpenseChart').getContext('2d');
        // var chartData = <?php echo $incomeexpenses; ?>;

        // var labels = chartData.map(function(e) {
        //     return e.month;
        // });
        // var incomeData = chartData.map(function(e) {
        //     return e.total_income;
        // });
        // var expensesData = chartData.map(function(e) {
        //     return e.total_expenses;
        // });

        // var incomeExpenseChart = new Chart(ctx, {
        //     type: 'bar',
        //     data: {
        //         labels: labels,
        //         datasets: [
        //             {
        //                 label: 'Income',
        //                 data: incomeData,
        //                 backgroundColor: 'rgba(75, 192, 192, 0.2)',
        //                 borderColor: 'rgba(75, 192, 192, 1)',
        //                 borderWidth: 1
        //             },
        //             {
        //                 label: 'Expenses',
        //                 data: expensesData,
        //                 backgroundColor: 'rgba(255, 99, 132, 0.2)',
        //                 borderColor: 'rgba(255, 99, 132, 1)',
        //                 borderWidth: 1
        //             }
        //         ]
        //     },
        //     options: {
        //         scales: {
        //             y: {
        //                 beginAtZero: true
        //             }
        //         }
        //     }
        // });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var ctx = document.getElementById('yearlyIncomeChart').getContext('2d');
            var chartData = <?php echo $yearlyIncomeData; ?>;

            // Get all unique years from the data
            var years = chartData.map(function(e) {
                return parseInt(e.year);
            });

            // Determine the min and max year in the data
            var minYear = Math.min.apply(null, years);
            var maxYear = Math.max.apply(null, years);

            // Populate the dropdown with 5-year intervals
            var select = document.getElementById('yearlyincomeSelect');
            for (var startYear = minYear; startYear <= maxYear; startYear += 5) {
                var endYear = startYear + 4;
                var option = document.createElement('option');
                option.value = startYear + '-' + endYear;
                option.textContent = startYear + ' - ' + endYear;
                select.appendChild(option);
            }

            // Initialize the chart
            var yearlyIncomeChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [], // Initially empty
                    datasets: [{
                        label: 'Yearly Income',
                        data: [],
                        backgroundColor: '#F9E8D9',
                        borderColor: '#F28543',
                        borderWidth: 2,
                        fill: false
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1000 // Adjust step size as needed
                            }
                        }
                    }
                }
            });

            // Function to update the chart
            function updateChart(selectedRange) {
                var [startYear, endYear] = selectedRange.split('-').map(Number);
                var filteredData = chartData.filter(function(e) {
                    var year = parseInt(e.year);
                    return year >= startYear && year <= endYear;
                });

                var labels = filteredData.map(function(e) {
                    return e.year;
                });
                var data = filteredData.map(function(e) {
                    return e.total_income;
                });

                yearlyIncomeChart.data.labels = labels;
                yearlyIncomeChart.data.datasets[0].data = data;
                yearlyIncomeChart.update();
            }

            // Event listener for dropdown selection
            select.addEventListener('change', function() {
                var selectedValue = select.value;
                if (selectedValue) {
                    updateChart(selectedValue);
                } else {
                    // If 'All Years' is selected, show data for all years
                    yearlyIncomeChart.data.labels = chartData.map(function(e) {
                        return e.year;
                    });
                    yearlyIncomeChart.data.datasets[0].data = chartData.map(function(e) {
                        return e.total_income;
                    });
                    yearlyIncomeChart.update();
                }
            });

            // Automatically show all years data on load
            select.value = '';
            select.dispatchEvent(new Event('change'));
        });

    </script>
    <!-- Include jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        function fetchUnreadMessages() {
            $.ajax({
            url: '../fetch_unread_count.php',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data && data.unread_messages !== undefined) {
                $('#unseenChatLabel').text(data.unread_messages);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("Error fetching unread messages:", textStatus, errorThrown);
            }
            });
        }

        // Run once on page load
        fetchUnreadMessages();

        // Poll every 3 seconds
        setInterval(fetchUnreadMessages, 3000);
    </script>
    <script>
        // Function to create and set the favicon
        function setFavicon(iconURL) {
        // Create a new link element
        const favicon = document.createElement('link');
        favicon.rel = 'icon';
        favicon.type = 'image/x-icon';
        favicon.href = iconURL;

        // Remove any existing favicons
        const existingIcons = document.querySelectorAll('link[rel="icon"]');
        existingIcons.forEach(icon => icon.remove());

        // Append the new favicon to the head
        document.head.appendChild(favicon);
        }

        // Example usage: set the favicon on page load
        document.addEventListener('DOMContentLoaded', () => {
        setFavicon('../asset/Renttrack pro logo.png'); // Change to your favicon path
        });
    </script>



    <?php include 'includes/footer.php'; ?>
