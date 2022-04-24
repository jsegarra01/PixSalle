PixSalle
The Student Council of La Salle saw that a lot of students are passionate about photography. They have posted their amazing photos on various social media accounts. To help the students develop their skills and inspire them to be professional photographers one day, the professors proposed to create a Photography web application.

Introduction
PixSalle is a new platform where students can freely showcase their work and express themselves through photography. This web application will allow them to be members, explore photos posted by other photographers and picture editors, view resources that they can use to improve their work and contribute to the knowledge sharing within the community through blogs. Be inspired by 500px and let your creativity soar and go wild.

Pre-requisites and requirements
To be able to create this web app, you are going to need a local environment suited with:

Web server (Nginx)
PHP 8
MySQL
BarCode
Composer
Git
You have to use the Docker local-environment set up that we have been using in class.

Requirements
Use Slim as the underlying framework.
Create and configure services in the dependencies.php file. Examples of services are Controllers, Repositories, ' view', 'flash', ...
Use Composer to manage all the dependencies of your application. There must be at least two dependencies.
Use Twig as the main template engine.
Use a CSS to stylize your application. Optionally, you may use a CSS framework.
Use MySQL as the main database management system.
Use Git to collaborate with your teammates.
All the code must be uploaded to the private Bitbucket repository that has been assigned to your team.
You must use Namespaces, Classes, and Interface.
Each member of the team must collaborate in the project with at least 10 commits. Each member must commit, at least, code regarding the View (twig), the Controller, and the Model.
Sections
Register and Login (this is already done for you, including associated cypress tests)
Landing page
Profile
Wallet
Memberships
Explore
Portfolio
Blogs
Landing Page
Anyone can view the landing page, even users who are not logged in.

This section describes the characteristics of the landing page of the application.

Endpoints	Method
/	GET
The landing page does not require user authentication. You need to implement a simple landing page where you will show a brief description, the main features and functionalities of PixSalle. Notice that this page is different from the Explore page (you will see it later). This page does not show a list of all the pictures in the system, but more of an informative page.

For this section, you will need to define anew or modify the base template we give you (twig inheritance). It is going to be used across all the pages of the application. This template must contain at least the following blocks:

head
This contains the title and the meta information of the page.
styles
This is where you will load all the required CSS and/or other styles.
header
This contains the navigation menu. The navigation menu must contain buttons to log in and sign up, and if the user is logged in, links to other features of the application (profile, portfolio, ...).
content
This is the main body of the webpage, depending on the feature being shown.
footer
Typically, this contains a copyright notice.
Feel free to add additional blocks as you consider necessary.

This qualification of this section includes the correct use of CSS and Semantic HTML for the whole project

Profile
The user must be logged in, and the functionality is per user.

This section describes the visualization and update of the user's personal information.

Endpoints	Method
/profile	GET
/profile	POST
/profile/changePassword	GET
/profile/changePassword	POST
If a user tries to access any of these endpoints manually without being authenticated, the web application must redirect him to the Login page with a warning message.

When a logged user accesses to the /profile endpoint, you need to display a form containing the following inputs:

username
email
phone
profile_picture
The email must be filled with the current stored information. The email address cannot be updated so the input must be disabled.

Each user has an associated id. Therefore, the default username is "user{id}", for example, user1. The user can change the username. The username must be alphanumeric. This will be the username to be displayed in the Quests and Blog sections. Make the necessary changes in the database to satisfy this requirement.

The phone number must follow the Spanish numbering plan. You have to validate phone numbers with the format "6XXXXXXXX".

The new input profile_picture must allow users to upload a profile picture. The requirements of the image are listed below:

The size of the image must be less than 1MB.
Only png and jpg images are allowed.
The image dimensions must be 500x500 (optionally, you can allow equal or less than 500x500). You can use this service to create example images. Also, be careful to not commit images to the remote repository.
You need to generate a UUID for the image and save it using the generated UUID as the image name (plus extension).
When the form is submitted, you need to validate the phone and profile_picture. If there is any error, you need to display them below the corresponding input.

Note: All the images must be stored inside an "uploads" folder inside the public folder of the server in order to be able to display them.

Below the form, you need to display a link named "Change Password" pointing to /profile/changePassword.

When a logged user accesses the /profile/changePassword endpoint, you need to display a "Reset Password" form containing the following inputs:

old_password
new_password
confirm_password
When the form is submitted, you need to do the following validations:

The old_password must match the current password stored in the database.
The new_password format must be the same used in the registration form
The confirm_password must match the value introduced in the new_password
If there is any error, you need to display again the form (all the inputs must be empty) and display a generic error . If all the validations have passed, the password of the user must be updated accordingly, and you need to display a success message below the form.

Note: Remember to store the password using the same hashing algorithm used in the registration.

Wallet
The user must be logged in, and the functionalities are per user.

To be able to use the services of the site, you must be a member. For that reason, the user will be able to pay for the membership prices using a wallet. These prices will be explained with more details in the next section.

Endpoints	Method
/user/wallet	GET
/user/wallet	POST
The users may be able to see the amount of money they have when they access /user/wallet. By default, when the user registers successfully, 30€ will be added to the wallet.

Aside from the user balance, there must be a form with one input:

amount: the amount of money the user wants to add to the wallet.
The amount should be greater than 0. If there is any error, it will redirect the user to the same page, clearing the amount of money that was entered before.

When the "Add to wallet" button is clicked, a POST request will be sent to the same endpoint to add money to the user's wallet.

Memberships
The user must be logged in, and the functionalities are per user.

This section describes the selection and visualization of the current membership of the user.

Endpoints	Method
/user/membership	GET
/user/membership	POST
When a logged user accesses the /user/membership page, the user must be able to see the current membership plan. If the user wishes to change the membership, they can click on another membership plan and click a button to make the membership change effective. After submitting the form, a POST request will be sent to the same endpoint.

There are 2 types of memberships: Cool and Active.

These memberships will allow users to have access to more functionalities. A user starts with a Cool membership, which is a free plan. The user doesn't have to pay anything. However, the user is restricted to only view albums, and they have full access to the blog section (create, read, update, delete).

The Active membership plan allows users to create albums. To create an album costs 2€. If any user wants to create an album and does not have enough money, they must be redirected to the wallet and a flash message should appear telling them about the error (not having enough money).

Explore
The user must be logged in, and the functionality is per user.

This section showcases all the pictures available in the system (from all users).

Endpoints	Method
/explore	GET
When a users accesses /explore without being logged in, the user will be redirected to the Login page. Otherwise, the user will see all the pictures in the system. You must display the username of the author along the picture.

Portfolio
The user must be logged in, and the functionality is per user.

A portfolio allows you to share your work externally, so you can build your own brand and market yourself as a professional photographer.

Endpoints	Method
/portfolio	GET
The user may be able to create a (only one) portfolio that consists of several albums.

When the user accesses to the /portfolio endpoint, you have to check if they have a portfolio created. If not, then there should be a button to allow the user to create one. If the user already has a portfolio, then all the album covers must be shown. When creating the portfolio, the user should be asked for a title, the tile must appear whenever the user accesses the portfolio endpoint.

Album
The user must be logged in, and the functionality is per user.

An album is a collection of photos that are uploaded by the user.

Endpoints	Method
/portfolio/album/{id}	GET
/portfolio/album/{id}	POST
/portfolio/album/{id}	DELETE
When users click on an album, they will access the /portfolio/album/{id} endpoint where id is the id of the album, and they will be able to see all the photos on the album.

To "upload" a photo, you must add an external link to a photo that has already been uploaded online. The user has to send a POST request to /portfolio/album/{id} with the URL of the image in the body of the request. Do not download the photo to your server, just save the URL.

A photo may only be added to a single album. If you wish to see the same photo in two different albums, you add the link to the photo in the other album.

For each album, you must be able to generate a QR Code using the Barcode API, this means that the QR code must encode the URL to that album. There must be a button to generate the QR Code and once it is generated, it should be displayed on the screen and a button should be added so it can be downloaded by the user so that it can be shared with other users. You can browse the documentation under the barcode folder included in this project. Notice that a docker service with the Barcode API has been included in the docker-compose.yml. Warning: keep an eye on the QR code images you generate, as they will start to take space on the remote repository if you save a lot of them in the server, and the repository may crash. Upload 2 QR images as maximum.

You can use barcode decoders to read the barcode image, or your smartphone. Keep in mind that your smartphone does not have acces to localhost, so you will not be able to access the server from there.

The application also allows the user to delete an album or a specific photo on an album. To delete an album, you must simply send a DELETE request to /portfolio/album/{id} specifying which album to delete. To delete a specific photo on an album, the id of the photo must be included in the body of the request. There should be buttons to delete the album and the photo.

If the user deletes an album, all the photos on the album will also be deleted from the system and therefore, it will no longer appear in the Explore section.

Blog
The user must be logged in, and the functionality is for all users.

This functionality will be tested using cypress. You can check the tests in the cypress folder.

You must implement a REST API that allows the user to create a blog entry, list all the blog entries, update a blog entry and delete a blog entry.

Note: These responses are for successful status codes of either 200 or 201, check the cypress tests. The field types are also defined and must be the ones shown.

Endpoints	Method	JSON request body	JSON response
/api/blog	GET		[{
 id: int
 title: string
 content: string
 userId: int
}]
/api/blog	POST	{
 title: string
 content: string
  userId: int
}	{
 id: int
 title: string
 content: string
 userId: int
}
/api/blog/{id}	GET		{
 id: int
 title: string
 content: string
 userId: int
}
/api/blog/{id}	PUT	{
 title: string
 content: string
}	{
 id: int
 title: string
 content: string
 userId: int
}
/api/blog/{id}	DELETE		{
 message: string
}
Aside from these specifications, the response message also depends on a failed status code:

Blog entry creation

Status code	Response body
400	{
 message: "'title' and/or 'content' and/or 'userId' key missing"
}
Getting blog entry information

Status code	Response body
404	{
 message: "Blog entry with id {id} does not exist"
}
Blog entry update

Status code	Response body
400	{
 message: "The title and/or content cannot be empty"
}
404	{
 message: "Blog entry with id {id} does not exist"
}
Blog entry deletion

You will have to figure them out by looking at the tests!

Webpages

To access the webpages, the users must use the following endpoints:

Endpoints	Method
/blog	GET
/blog/{id}	GET
The /blog endpoint is a page where all the users can see a list of all the blog entries by all users that have been added to the blog. Each blog post in the list must have a link to the specific post with additional information.

<div id="blog-list"><div>
To show a specific blog entry, the user must access the /blog/{id} endpoint. For each blog entry, you must at least show the title, content, and the author. The content may have multiple paragraphs.

Delivery
Since you are using Git, and also we want to make this project as real as possible, you are going to use annotated tags in order to release new versions of your application. You can check the official git documentation on how to create tags and use them. Remember to push your tags to the Bitbucket repository, otherwise they will only stay in your local computer.

Also, remember to add the SQL code to the docker-entrypoint-db folder as a script. This will allow the automatic creation of the database tables when running docker-compose.

This project is going to be delivered in two phases. As you may have noticed, this project has 7 different sections, and they are ordered sequentially. There will be a checkpoint were you will need to deliver a new release/version of your application containing the required sections. You can check here the dates with all the expected deliveries.

v1.0.0 on May 1 - Sections from 1 to 5
v2.0.0 on May 22 - All sections
The first delivery is the checkpoint of the project. The last delivery is the whole project.

Evaluation
To evaluate the project, we will use the release v2.0.0 of your repository.
In May, all the teams that have delivered the final release on time, will be interviewed by the teachers.
In this interview we are going to validate that each team member have worked and collaborated as expected in the project.
Read the syllabus for next steps in case you pass, fail, or you are not elegible for the interview.
Evaluation criteria
v1.0.0

To score the first release v1.0.0, the distribution of points are as follows:

Landing (Semantic HTML, CSS of the whole project) - 1.5p
Profile - 3.5p
Memberships - 1.5p
Explore - 2.5p
Other criteria (clean code quality, clean design,...) - 1p
v2.0.0

As mentioned above, the last delivery v2.0.0 will have the final grade of the whole web application (PixSalle). It must have ALL sections implemented. The distribution of points are as follows:

Landing (Semantic HTML, CSS of the whole project) - 0.5p
Profile - 1.5p
Wallet - 0.5p
Memberships - 0.5p
Explore - 0.5p
Portfolio - 2.75p
Blog - 2.75p
Other criteria (clean code quality, clean design,...) - 1p
