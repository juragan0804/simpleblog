<?php
include 'includes/config.php';
session_start();
include 'includes/header.php';
?>

<article>
    <h2>About This Blog</h2>
    <p>This blog allows users to create, manage, and read blog posts. The application also includes user management features that are accessible only by admins.</p>

    <h3>Key Features</h3>
    <ul>
        <li><strong>Post Management</strong>:
            <ul>
                <li>Users can create, edit, and delete their own posts.</li>
                <li>Admins can view, edit, and delete all posts.</li>
            </ul>
        </li>
        <li><strong>User Management</strong>:
            <ul>
                <li>Admins can view, edit, and delete users.</li>
                <li>Admins can change user roles (user or admin).</li>
            </ul>
        </li>
    </ul>

    <h3>Program Flow</h3>
    <p>The following diagrams illustrate the program flow and data flow within the application.</p>

    <h4>Data Flow Diagram (DFD)</h4>

    <h5>Level 0 (Context Diagram)</h5>
    <pre>
 +---------------------+                              +---------------------+
 |                     |                              |                     |
 |     User            |<-------- Interactions ------>|       Blog         |
 |                     |                              |                     |
 +---------------------+                              +---------------------+
                                                         |
                                                         |
 +---------------------+                              +---------------------+
 |                     |<-------- Interactions ------>|       Admin        |
 |                     |                              |                     |
 +---------------------+                              +---------------------+
    </pre>

    <h5>Level 1 (DFD)</h5>
    <pre>
 +---------------------+                            +----------------------+
 |                     |                            |                      |
 |     User            |                            |      Admin           |
 |                     |                            |                      |
 +---------+-----------+                            +----------+-----------+
           |                                               |
           |                                               |
           v                                               v
 +---------+-----------+                        +----------+-----------+
 |                     |                        |                      |
 |  Manage Posts       |<---- Read & Write ---->|  Manage Posts        |
 |                     |                        |                      |
 +---------+-----------+                        +----------+-----------+
           |                                               |
           |                                               |
           v                                               v
 +---------+-----------+                        +----------+-----------+
 |                     |                        |                      |
 |  Create Post        |                        |  Manage Users        |
 |                     |                        |                      |
 +---------------------+                        +----------------------+
    </pre>

    <h3>Technologies Used</h3>
    <ul>
        <li>PHP</li>
        <li>MySQL</li>
        <li>HTML</li>
        <li>CSS</li>
    </ul>

    <h3>How to Use</h3>
    <ol>
        <li><strong>Clone the Repository</strong>:
            <pre>
git clone https://github.com/yourusername/simple-blog.git
            </pre>
        </li>
        <li><strong>Configure the Database</strong>:
            <ul>
                <li>Create a database in MySQL.</li>
                <li>Import the `database.sql` file into the database.</li>
                <li>Configure the database connection in the `includes/config.php` file.</li>
            </ul>
        </li>
        <li><strong>Run the Application</strong>:
            <ul>
                <li>Open a browser and access the application through the URL configured on your server.</li>
            </ul>
        </li>
    </ol>

    <h3>Contribution</h3>
    <p>Feel free to submit a pull request or open an issue for contributions and improvements.</p>

    <h3>License</h3>
    <p>This application is licensed under the MIT License. See the `LICENSE` file for more details.</p>

    <h3>Team Members</h3>
    <ul>
        <li>Achmad Aris Setiawan</li>
        <li>Hilman</li>
        <li>Devi</li>
        <li>Nasikin</li>
        <li>Vero</li>
    </ul>
</article>

<?php include 'includes/footer.php'; ?>
