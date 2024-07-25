function _1(md){return(
md`<div style="color: grey; font: 13px/25.5px var(--sans-serif); text-transform: uppercase;"><h1 style="display: none;">Bar chart</h1><a href="https://d3js.org/">D3</a> › <a href="/@d3/gallery">Gallery</a></div>

# Bar chart

This chart shows the relative frequency of letters in the English language. A vertical bar chart such as this is sometimes called a *column* chart. Data: *Cryptological Mathematics*, Robert Lewand`
)}

function _chart(d3,data1)
{
  // Declare the chart dimensions and margins.
  const barWidth = 79
  const height = 500;
  const marginTop = 30;
  const marginRight = 0;
  const marginBottom = 50;
  const marginLeft = 40;
  const width = data1.length * barWidth + marginTop + marginBottom

  // Declare the x (horizontal position) scale.
  const x = d3.scaleBand()
      .domain(d3.groupSort(data1, ([d]) => -d.Total_Obligation, (d) => d.Agency_Name)) // descending frequency
      .range([marginLeft, width - marginRight])
      .padding(0.1);
  
  // Declare the y (vertical position) scale.
  const y = d3.scaleLinear()
      .domain([0, d3.max(data1, (d) => d.Total_Obligation)])
      .range([height - marginBottom, marginTop]);

  // Create the SVG container.
  const svg = d3.create("svg")
      .attr("width", width)
      .attr("height", height)
      .attr("viewBox", [0, 0, width, height])
      .attr("style", "max-width: 100%; height: auto;");

  // Add a rect for each bar.
  svg.append("g")
      .attr("fill", "steelblue")
    .selectAll()
    .data(data1)
    .join("rect")
      .attr("x", (d) => x(d.Agency_Name))
      .attr("y", (d) => y(d.Total_Obligation))
      .attr("height", (d) => y(0) - y(d.Total_Obligation))
      .attr("width", x.bandwidth());

  // Add the x-axis and label.
  const axis = svg.append("g")
      .attr("transform", `translate(0,${height - marginBottom})`)
      .call(d3.axisBottom(x).tickSizeOuter(0));

  // Add the y-axis and label, and remove the domain line.
  svg.append("g")
      .attr("transform", `translate(${marginLeft},0)`)
      .call(d3.axisLeft(y).tickFormat(d => (d / 1e9).toFixed(1)))
      .call(g => g.select(".domain").remove())
      .call(g => g.append("text")
          .attr("x", -marginLeft)
          .attr("y", 10)
          .attr("fill", "currentColor")
          .attr("text-anchor", "start")
          .text("↑ Total Obligation ($ in billions)"));

  setTimeout(() => {
    axis.selectAll(".tick text")
        .style("font-family", "HelveticaNeueLTStd-Cn")
        .style("font-size", "9px")
        .call(wrap, x.bandwidth());
  }, 0);

  // Return the SVG element.
  return svg.node();

  // Function to wrap text
  function wrap(text, width) {
    text.each(function() {
      var text = d3.select(this),
          words = text.text().split(/\s+/).reverse(),
          word,
          line = [],
          lineNumber = 0,
          lineHeight = 1.1, // ems
          y = text.attr("y"),
          dy = parseFloat(text.attr("dy")),
          tspan = text.text(null).append("tspan").attr("x", 0).attr("y", y).attr("dy", dy + "em");
      while (word = words.pop()) {
        line.push(word);
        tspan.text(line.join(" "));
        if (tspan.node().getComputedTextLength() > width) {
          line.pop();
          tspan.text(line.join(" "));
          line = [word];
          tspan = text.append("tspan").attr("x", 0).attr("y", y).attr("dy", ++lineNumber * lineHeight + dy + "em").text(word);
        }
      }
    });
  }
}


function _data1(FileAttachment){return(
FileAttachment("agency@2.csv").csv({typed: "auto"})
)}

function _agency2(__query,FileAttachment,invalidation){return(
__query(FileAttachment("agency@2.csv"),{from:{table:"agency"},sort:[],slice:{to:null,from:null},filter:[],select:{columns:null}},invalidation)
)}

function _data2(FileAttachment){return(
FileAttachment("recipient@1.csv").csv({typed: "auto"})
)}

function _recipient1(__query,FileAttachment,invalidation){return(
__query(FileAttachment("recipient@1.csv"),{from:{table:"recipient"},sort:[],slice:{to:null,from:null},filter:[],select:{columns:["Recipient_Name","Total_Obligation"]}},invalidation)
)}

function _chart2(d3,data2)
{
  // Declare the chart dimensions and margins.
  const barWidth = 79
  const height = 500;
  const marginTop = 30;
  const marginRight = 0;
  const marginBottom = 80;
  const marginLeft = 40;
  const width = data2.length * barWidth + marginTop + marginBottom

  // Declare the x (horizontal position) scale.
  const x = d3.scaleBand()
      .domain(d3.groupSort(data2, ([d]) => -d.Total_Obligation, (d) => d.Recipient_Name)) // descending frequency
      .range([marginLeft, width - marginRight])
      .padding(0.1);
  
  // Declare the y (vertical position) scale.
  const y = d3.scaleLinear()
      .domain([0, d3.max(data2, (d) => d.Total_Obligation)])
      .range([height - marginBottom, marginTop]);

  // Create the SVG container.
  const svg = d3.create("svg")
      .attr("width", width)
      .attr("height", height)
      .attr("viewBox", [0, 0, width, height])
      .attr("style", "max-width: 100%; height: auto;");

  // Add a rect for each bar.
  svg.append("g")
      .attr("fill", "steelblue")
    .selectAll()
    .data(data2)
    .join("rect")
      .attr("x", (d) => x(d.Recipient_Name))
      .attr("y", (d) => y(d.Total_Obligation))
      .attr("height", (d) => y(0) - y(d.Total_Obligation))
      .attr("width", x.bandwidth());

  // Add the x-axis and label.
  const axis = svg.append("g")
      .attr("transform", `translate(0,${height - marginBottom})`)
      .call(d3.axisBottom(x).tickSizeOuter(0));

  // Add the y-axis and label, and remove the domain line.
  svg.append("g")
      .attr("transform", `translate(${marginLeft},0)`)
      .call(d3.axisLeft(y).tickFormat(d => (d / 1e9).toFixed(1)))
      .call(g => g.select(".domain").remove())
      .call(g => g.append("text")
          .attr("x", -marginLeft)
          .attr("y", 10)
          .attr("fill", "currentColor")
          .attr("text-anchor", "start")
          .text("↑ Total Obligation ($ in billions)"));

  setTimeout(() => {
    axis.selectAll(".tick text")
        .style("font-family", "HelveticaNeueLTStd-Cn")
        .style("font-size", "9px")
        .call(wrap, x.bandwidth());
  }, 0);

  // Return the SVG element.
  return svg.node();

  // Function to wrap text
  function wrap(text, width) {
    text.each(function() {
      var text = d3.select(this),
          words = text.text().split(/\s+/).reverse(),
          word,
          line = [],
          lineNumber = 0,
          lineHeight = 1.1, // ems
          y = text.attr("y"),
          dy = parseFloat(text.attr("dy")),
          tspan = text.text(null).append("tspan").attr("x", 0).attr("y", y).attr("dy", dy + "em");
      while (word = words.pop()) {
        line.push(word);
        tspan.text(line.join(" "));
        if (tspan.node().getComputedTextLength() > width) {
          line.pop();
          tspan.text(line.join(" "));
          line = [word];
          tspan = text.append("tspan").attr("x", 0).attr("y", y).attr("dy", ++lineNumber * lineHeight + dy + "em").text(word);
        }
      }
    });
  }
}


export default function define(runtime, observer) {
  const main = runtime.module();
  function toString() { return this.url; }
  const fileAttachments = new Map([
    ["recipient@1.csv", {url: new URL("./files/receipient.csv", import.meta.url), mimeType: "text/csv", toString}],
    ["agency@2.csv", {url: new URL("./files/agency.csv", import.meta.url), mimeType: "text/csv", toString}]
  ]);
  main.builtin("FileAttachment", runtime.fileAttachments(name => fileAttachments.get(name)));
  main.variable().define(["md"], _1);
  main.variable(observer("chart")).define("chart", ["d3","data1"], _chart);
  main.variable().define("data1", ["FileAttachment"], _data1);
  main.variable().define("agency2", ["__query","FileAttachment","invalidation"], _agency2);
  main.variable().define("data2", ["FileAttachment"], _data2);
  main.variable().define("recipient1", ["__query","FileAttachment","invalidation"], _recipient1);
  main.variable(observer("chart2")).define("chart2", ["d3","data2"], _chart2);
  return main;
}
