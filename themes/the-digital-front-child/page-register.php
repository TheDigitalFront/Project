<?php

/**
 * Template Name: Registration Page
 *
 * Front-end user registration for The Digital Front.
 * @package TheDigitalFront
 * @since   1.0.0
 */

get_header();
?>

<main class="tdf-page">
    <div class="tdf-container tdf-container--narrow">

        <?php if (function_exists('yoast_breadcrumb')) : ?>
            <nav class="tdf-breadcrumbs" aria-label="Breadcrumb">
                <?php yoast_breadcrumb('<p>', '</p>'); ?>
            </nav>
        <?php endif; ?>

        <header class="tdf-page__header">
            <h1 class="tdf-page__title">Create an Account</h1>
        </header>

        <div class="tdf-page__content">

            <?php if (is_user_logged_in()) : ?>

                <p>
                    You are already logged in.
                    <a href="<?php echo esc_url(home_url('/')); ?>">Go to Home</a>
                </p>

            <?php else : ?>

                <?php
                // Show error messages passed back via URL (e.g. ?registration=disabled).
                if (isset($_GET['registration']) && $_GET['registration'] === 'disabled') :
                ?>
                    <p class="tdf-form__error">User registration is currently disabled.</p>
                <?php endif; ?>

                <form
                    class="tdf-form"
                    method="post"
                    action="<?php echo esc_url(site_url('wp-login.php?action=register', 'login_post')); ?>">
                    <div class="tdf-form__group">
                        <label class="tdf-form__label" for="user_login">Username</label>
                        <input
                            class="tdf-form__input"
                            type="text"
                            name="user_login"
                            id="user_login"
                            autocomplete="username"
                            required />
                    </div>

                    <div class="tdf-form__group">
                        <label class="tdf-form__label" for="user_email">Email Address</label>
                        <input
                            class="tdf-form__input"
                            type="email"
                            name="user_email"
                            id="user_email"
                            autocomplete="email"
                            required />
                    </div>

                    <p class="tdf-form__note">A password will be sent to your email address.</p>

                    <?php
                    /**
                     * Hook: tdf_register_form
                     * Allows plugins (e.g. WPForms Lite) to inject additional
                     * fields into the registration form if needed later.
                     */
                    do_action('register_form');
                    ?>

                    <div class="tdf-form__group">
                        <button type="submit" class="tdf-btn tdf-btn--primary">
                            Register
                        </button>
                    </div>

                </form>

                <p class="tdf-form__alt-link">
                    Already have an account?
                    <a href="<?php echo esc_url(wp_login_url()); ?>">Log in here</a>
                </p>

            <?php endif; ?>

        </div>
    </div>
</main>

<?php get_footer(); ?>