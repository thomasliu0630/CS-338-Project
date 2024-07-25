import define1 from "./a33468b95d0b15b0@817.js";

function _obligation_outlayed(FileAttachment){return(
FileAttachment("data@1.csv").csv({typed: true})
)}

function _us(FileAttachment){return(
FileAttachment("counties-albers-10m.json").json()
)}

function _valuemap(obligation_outlayed){return(
new Map(obligation_outlayed.map(d => [d.State, d.Total_Obligation]))
)}

function _formatInTensOfBillions(){return(
function formatInTensOfBillions(value) {
  return (value / 1e10).toFixed(0);  // Divide by 10 billion and format as integer
}
)}

function _valuemap2(obligation_outlayed){return(
new Map(obligation_outlayed.map(d => [d.State, d.Total_Outlayed]))
)}

function _formatInBillions(){return(
function formatInBillions(value) {
  return (value / 1e9).toFixed(0);  // Divide by 1 billion and format as integer
}
)}

function _12(Plot,formatInTensOfBillions,topojson,us,valuemap){return(
Plot.plot({
  projection: "identity",
  width: 975,
  height: 610,
  color: {scheme: "Blues", type: "quantize", n: 10, domain: [100000, 90000000000], label: "Total Obligation ($ in tens of billions)", legend: true, tickFormat: formatInTensOfBillions},
  marks: [
    Plot.geo(topojson.feature(us, us.objects.states), Plot.centroid({
      fill: d => valuemap.get(d.properties.name),
      title: d => `${d.properties.name}\n${new Intl.NumberFormat('en-US').format(valuemap.get(d.properties.name))}`,
      tip: true
    })),
    Plot.geo(topojson.mesh(us, us.objects.states, (a, b) => a !== b), {stroke: "white"})
 ]
})
)}

function _15(Plot,formatInBillions,topojson,us,valuemap2){return(
Plot.plot({
  projection: "identity",
  width: 975,
  height: 610,
  color: {scheme: "Blues", type: "quantize", n: 10, domain: [100000, 21000000000], label: "Total Outlayed ($ in billions)", legend: true, tickFormat: formatInBillions},
  marks: [
    Plot.geo(topojson.feature(us, us.objects.states), Plot.centroid({
      fill: d => valuemap2.get(d.properties.name),
      title: d => `${d.properties.name}\n${new Intl.NumberFormat('en-US').format(valuemap2.get(d.properties.name))}`,
      tip: true
    })),
    Plot.geo(topojson.mesh(us, us.objects.states, (a, b) => a !== b), {stroke: "white"})
 ]
})
)}

export default function define(runtime, observer) {
  const main = runtime.module();
  function toString() { return this.url; }
  const fileAttachments = new Map([
    ["data@1.csv", {url: new URL("./files/138ff3103e06f8b7001560842903392710c7950a7a02ecee26ba056d256d9f127a8bd80abd83e897c9f68ff625f134501424fff4d8ef8c235638d0f0d287766b.csv", import.meta.url), mimeType: "text/csv", toString}],
    ["counties-albers-10m.json", {url: new URL("./files/1ec3edc43ba66c0db419744c479d1b5118bb405587189f3ad739a10853f6f933d86824e809f4b4ad18053ab33fb5dc7c5f3d6bc601654c8ea976afd5b321f517.json", import.meta.url), mimeType: "application/json", toString}]
  ]);
  main.builtin("FileAttachment", runtime.fileAttachments(name => fileAttachments.get(name)));

  main.variable().define("obligation_outlayed", ["FileAttachment"], _obligation_outlayed);
  main.variable().define("us", ["FileAttachment"], _us);
  main.variable().define("valuemap", ["obligation_outlayed"], _valuemap);
  main.variable().define("formatInTensOfBillions", _formatInTensOfBillions);
  main.variable().define("valuemap2", ["obligation_outlayed"], _valuemap2);
  main.variable().define("formatInBillions", _formatInBillions);
  
  // Defining the main graphs
  main.variable(observer()).define(["Plot","formatInTensOfBillions","topojson","us","valuemap"], _12);
  main.variable(observer()).define(["Plot","formatInBillions","topojson","us","valuemap2"], _15);

  return main;
}
