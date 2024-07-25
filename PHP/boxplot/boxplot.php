<!DOCTYPE html>
<meta charset="utf-8">

<!-- Load d3.js -->
<script src="https://d3js.org/d3.v4.js"></script>
<ul>
    <a href="../index.php"><strong>Main Menu</strong></a> - Return to main menu
</ul>

<!-- Create divs where the graphs will take place -->
<div id="my_dataviz_non_covid">
  <h2>Non-Covid Awards</h2>
</div>
<div id="my_dataviz_covid">
  <h2>Covid Awards</h2>
</div>

<script>

// Function to create box plot
function createBoxPlot(divId, csvFile) {
  // set the dimensions and margins of the graph
  var margin = {top: 10, right: 30, bottom: 30, left: 70},
      width = 460 - margin.left - margin.right,
      height = 700 - margin.top - margin.bottom;

  // append the svg object to the body of the page
  var svg = d3.select(divId)
    .append("svg")
      .attr("width", width + margin.left + margin.right)
      .attr("height", height + margin.top + margin.bottom)
    .append("g")
      .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

  // Read the data and compute summary statistics for each agency
  d3.csv(csvFile, function(data) {

    // Compute quartiles, median, inter quantile range min and max --> these info are then used to draw the box.
    var sumstat = d3.nest() // nest function allows to group the calculation per level of a factor
      .key(function(d) { return d.Agency_Name;})
      .rollup(function(d) {
        q1 = d3.quantile(d.map(function(g) { return +g.Outlayed_Amount;}).sort(d3.ascending),.25)
        median = d3.quantile(d.map(function(g) { return +g.Outlayed_Amount;}).sort(d3.ascending),.5)
        q3 = d3.quantile(d.map(function(g) { return +g.Outlayed_Amount;}).sort(d3.ascending),.75)
        interQuantileRange = q3 - q1
        min = q1 - 1.5 * interQuantileRange
        max = q3 + 1.5 * interQuantileRange
        return({q1: q1, median: median, q3: q3, interQuantileRange: interQuantileRange, min: min, max: max})
      })
      .entries(data)

    // Show the X scale
    var x = d3.scaleBand()
      .range([ 0, width ])
      .domain(sumstat.map(function(d) { return d.key; }))
      .paddingInner(1)
      .paddingOuter(.5)
    svg.append("g")
      .attr("transform", "translate(0," + height + ")")
      .call(d3.axisBottom(x))

    // Show the Y scale
    var y = d3.scaleLinear()
      .domain([d3.min(sumstat, function(d) { return d.value.min; }), d3.max(sumstat, function(d) { return d.value.max; })])
      .range([height, 0])
    svg.append("g").call(d3.axisLeft(y))

    // Show the main vertical line
    svg
      .selectAll("vertLines")
      .data(sumstat)
      .enter()
      .append("line")
        .attr("x1", function(d){return(x(d.key))})
        .attr("x2", function(d){return(x(d.key))})
        .attr("y1", function(d){return(y(d.value.min))})
        .attr("y2", function(d){return(y(d.value.max))})
        .attr("stroke", "black")
        .style("width", 40)

    // rectangle for the main box
    var boxWidth = 100
    svg
      .selectAll("boxes")
      .data(sumstat)
      .enter()
      .append("rect")
          .attr("x", function(d){return(x(d.key)-boxWidth/2)})
          .attr("y", function(d){return(y(d.value.q3))})
          .attr("height", function(d){return(y(d.value.q1)-y(d.value.q3))})
          .attr("width", boxWidth )
          .attr("stroke", "black")
          .style("fill", "#69b3a2")

    // Show the median
    svg
      .selectAll("medianLines")
      .data(sumstat)
      .enter()
      .append("line")
        .attr("x1", function(d){return(x(d.key)-boxWidth/2) })
        .attr("x2", function(d){return(x(d.key)+boxWidth/2) })
        .attr("y1", function(d){return(y(d.value.median))})
        .attr("y2", function(d){return(y(d.value.median))})
        .attr("stroke", "black")
        .style("width", 80)
  })
}

// Create the non-covid box plot
createBoxPlot("#my_dataviz_non_covid", "../boxplot/csv/non_covid_awards.csv");

// Create the covid box plot
createBoxPlot("#my_dataviz_covid", "../boxplot/csv/covid_awards.csv");

</script>

