# Simple-HMS
Simple health dashoard to track general information about the user <br/>
• Authentication pages: <br/>
&ensp; The 2 authentication pages where HTML/CSS templates that where modified to add height and weight fields and remove the phone nb field. <br/>
&ensp; The registration page takes the input fields and submits them to the database after validation. <br/>

![image](https://github.com/Abdo-Fahmi/Simple-HMS/assets/153271309/f1835400-cf4b-4a80-b8cd-29860891a5d0)

• Dashboard and style: <br/>
&ensp; The majority of the styling/element arrangement is custom, only the bases on the nav bar where templates. <br/>
• Graphs and Chart.js: <br/>
&ensp; The charts are drawn using chart.js, the data in the charts is fetched from the database and fed to the charts using php, in addition extra features such ass data filters where custom. <br/>

![image](https://github.com/Abdo-Fahmi/Simple-HMS/assets/153271309/440b6c0a-80d5-4094-bd5b-5a8a763b8108)

![image](https://github.com/Abdo-Fahmi/Simple-HMS/assets/153271309/8126cd79-a8f8-47d0-8a8c-0235d70a7b4b)

 
## Features
  1. Weight tracker: A graph that will keep track of the user’s weight over time using measurements they input alongside the date those measurements where made. The view of the graph can be filtered between dates as the user wishes.
  2. Calorie tracker: A chart that keeps tracks of the calorie intake of the user as they input the caloric value of each meal they eat that day, the user may also filter this chart by today or all, in order to see how many days they where over or under their daily needed calories.
  3. Event tracker: It is an interface for the user to add and track upcoming events, the user may see exactly how much time is left for the event as well as see the title and description they provided when adding the event. The system will indicate when an event’s time is up and the user may delete events they add.

## Database
  The database was implemented and managed using PHPMyAdmin  which provided an interface for table creation, record insertion and the implementation of triggers for the tables o utilize and keep the data up to date will any new insertions and deletions all of which will be mentioned and discussed below.<br/>
	The system communicates with the database using MySQLi prepared statements.

## Security
  Following best practice, all passwords are stored hashed and not in plain text, database queries are made and executed using MySQLi prepared statements in the object oriented method.
