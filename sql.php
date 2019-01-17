<?php
// 这里是一些经典的sql练习

// 下面是mysql having的用法：
// 显示每个地区的总人口数和总面积．仅显示那些面积超过1000000的地区。
// SELECT region, SUM(population), SUM(area)
// FROM bbc
// GROUP BY region
// HAVING SUM(area)>1000000