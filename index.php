<?php
date_default_timezone_set('UTC');

$flights = [
  [
    "name" => "Alice",
    "depart" => [
      "airport" => "SFO",
      "time" => date_create("12/01/2015 02:00:00")
    ],
    "arrive" => [
      "airport" => "NAS",
      "time" => date_create("12/01/2015 05:30:00")
    ]
  ],
  [
    "name" => "Bob",
    "depart" => [
      "airport" => "JFK",
      "time" => date_create("12/01/2015 00:00:00")
    ],
    "arrive" => [
      "airport" => "NAS",
      "time" => date_create("12/01/2015 04:00:00")
    ]
  ],
  [
    "name" => "Chuck",
    "depart" => [
      "airport" => "JFK",
      "time" => date_create("11/30/2015 22:00:00")
    ],
    "arrive" => [
      "airport" => "NAS",
      "time" => date_create("12/01/2015 05:05:00")
    ]
  ],
  [
    "name" => "Doug",
    "depart" => [
      "airport" => "LHR",
      "time" => date_create("11/30/2015 10:00:00")
    ],
    "arrive" => [
      "airport" => "NAS",
      "time" => date_create("12/01/2015 04:05:00")
    ]
  ],
];

?>


<html>
 <head>
  <title>PHP Test</title>
  <script src="//d3js.org/d3.v3.min.js" charset="utf-8"></script>
 </head>
 <body>
   <div id="figure"></div>

   <script>
   var data = <?php echo json_encode($flights) ?>
   </script>

   <script>

var margin = {top: 50, right: 20, bottom: 10, left: 65},
    width = 800 - margin.left - margin.right,
    height = 500 - margin.top - margin.bottom;

var y = d3.scale.ordinal()
    .rangeRoundBands([0, height], .3);

var x = d3.time.scale()
    .rangeRound([0, width]);

var color = d3.scale.category20();

var xAxis = d3.svg.axis()
    .scale(x)
    .orient("top");

var yAxis = d3.svg.axis()
    .scale(y)
    .orient("left")

var svg = d3.select("#figure").append("svg")
    .attr("width", width + margin.left + margin.right)
    .attr("height", height + margin.top + margin.bottom)
    .attr("id", "d3-plot")
  .append("g")
    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

function parseDate(time) {
  return new Date(time.date.replace(" ", "T"))
}

data.forEach(function(d) {
  d.depart.datetime = parseDate(d.depart.time);
  d.arrive.datetime = parseDate(d.arrive.time);
});

var airports = data.map(function(d) {
    return d.depart.airport;
  }).reduce(function(a,b){
    if (a.indexOf(b) < 0 ) a.push(b);
    return a;
  },[]);
color.domain(airports);

var min_val = d3.min(data, function(d) { return d.depart.datetime; });
var max_val = d3.max(data, function(d) { return d.arrive.datetime; });
x.domain([min_val, max_val]).nice();

y.domain(data.map(function(d) { return d.name; }));

svg.append("g")
    .attr("class", "x axis")
    .call(xAxis);

svg.append("g")
    .attr("class", "y axis")
    .call(yAxis)

var sections = svg.selectAll(".flight")
      .data(data)
    .enter().append("g")
      .attr("class", "bar")
      .attr("transform", function(d) { return "translate(0," + y(d.name) + ")"; });

var bars = sections.selectAll("rect")
      .data(function(d) { return [d]; })
    .enter().append("g").attr("class", "subbar");

bars.append("rect")
    .attr("height", y.rangeBand())
    .attr("x", function(d) { console.log(d.depart.datetime); return x(d.depart.datetime); })
    .attr("width", function(d) { return x(d.arrive.datetime) - x(d.depart.datetime); })
    .style("fill", function(d) { return color(d.depart.airport); });

bars.append("text")
    .attr("x", function(d) { return x(d.depart.datetime); })
    .attr("y", y.rangeBand()/2)
    .attr("dy", "0.5em")
    .attr("dx", "0.5em")
    .style("font" ,"10px sans-serif")
    .style("text-anchor", "begin")
    .text(function(d) { return d.depart.airport + " to " + d.arrive.airport });

sections.insert("rect",":first-child")
    .attr("height", y.rangeBand())
    .attr("x", "1")
    .attr("width", width)
    .attr("fill-opacity", "0.5")
    .style("fill", "#F5F5F5")
    .attr("class", function(d,index) { return index%2==0 ? "even" : "uneven"; });

svg.append("g")
    .attr("class", "y axis")
.append("line")
    .attr("x1", x(0))
    .attr("x2", x(0))
    .attr("y2", height);

d3.selectAll(".axis path")
    .style("fill", "none")
    .style("stroke", "#000")
    .style("shape-rendering", "crispEdges")

d3.selectAll(".axis line")
    .style("fill", "none")
    .style("stroke", "#000")
    .style("shape-rendering", "crispEdges")
</script>

 </body>
</html>
