document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.delete-post');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            const postId = this.getAttribute('data-id');

            if (confirm('Are you sure you want to delete this post?')) {
                fetch(`delete_post.php?id=${postId}`, {
                    method: 'DELETE',
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Post deleted successfully');
                        window.location.href = 'index.php';
                    } else {
                        alert('Error deleting post: ' + data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        });
    });
});
