<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Home</title>
    <link rel="stylesheet" href="/assets/css/Styles.css">
</head>
<style>
    /* Weekly Specials Section */
    .weekly-specials {
        text-align: center;
        margin: 40px 0;
    }

    .weekly-specials h2 {
        font-size: 2rem;
        margin-bottom: 20px;
        font-family: 'Dancing Script', cursive;
    }


    .specials-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
        justify-items: center;
    }


    .special-item {
        width: 100%;
        height: 300px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        background-color: #f9f9f9;
        border-radius: 10px;
        overflow: hidden;
    }


    .special-item img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-bottom: 2px solid #ddd;
    }

    .special-item p {
        margin-top: 10px;
        font-size: 1.1rem;
        font-weight: bold;
        color: #333;
    }
</style>

<body>

    <?php include "../../includes/navbar-customer.php"; ?>

    <!-- Hero Section -->
    <section id="hero">
        <img src="images/hero-image.jpg" alt="Cozy Restaurant" class="hero-image">
        <h2>Where Every Bite Feels Like Home</h2>
    </section>

    <!-- Specials Carousel -->
    <section class="weekly-specials">
        <h2>Weekly Specials</h2>
        <div class="specials-container">
            <div class="special-item">
                <img src="images/sp1.jpg" alt="Special Dish 1">
                <p>Special Dish 1</p>
            </div>
            <div class="special-item">
                <img src="images/sp2.jpg" alt="Special Dish 2">
                <p>Special Dish 2</p>
            </div>
            <div class="special-item">
                <img src="images/sp3.jpg" alt="Special Dish 3">
                <p>Special Dish 3</p>
            </div>
            <div class="special-item">
                <img src="images/sp4.jpg" alt="Special Dish 3">
                <p>Special Dish 4 </p>
            </div>
            <div class="special-item">
                <img src="images/sp5.jpg" alt="Special Dish 3">
                <p>Special Dish 5</p>
            </div>
        </div>
    </section>


    <!-- google map view embedded -->
    <div>
        <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d247.61186169256146!2d79.90312124966417!3d6.795412548542516!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2slk!4v1732603671726!5m2!1sen!2slk" width="400" height="300" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>

    <?php include "../../includes/footer.php"; ?>


</body>

</html>