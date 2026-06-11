<?php
session_start();

$base_path = "../";
include("../config/db.php");
include("../includes/functions.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "farmer") {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$msg = "";

/* =======================
   SUBMIT QUESTION
======================= */
if (isset($_POST['ask'])) {

    $question = escape($conn, $_POST['question']);

    $conn->query("
        INSERT INTO questions (user_id, question)
        VALUES ($user_id, '$question')
    ");

    $msg = "Your question has been submitted! An expert will respond soon.";
}

/* =======================
   GET QUESTIONS + ANSWERS
======================= */
$my_questions = $conn->query("
    SELECT *
    FROM questions
    WHERE user_id = $user_id
    ORDER BY created_at DESC
");

$page_title = "Ask Expert";

include("../includes/header.php");
?>

<h1 class="page-title">💬 Ask an Agricultural Expert</h1>

<p class="page-subtitle">
    Submit farming questions and get expert answers
</p>

<?php if ($msg): ?>
    <div class="alert alert-success">
        <?php echo htmlspecialchars($msg); ?>
    </div>
<?php endif; ?>

<!-- =======================
     ASK FORM
======================= -->
<div class="card">

    <h3>Submit a Question</h3>

    <form method="POST">

        <div class="form-group">
            <label>Your Question</label>

            <textarea
                name="question"
                placeholder="e.g. How do I control rice pests in the rainy season?"
                required
            ></textarea>
        </div>

        <button type="submit" name="ask" class="btn btn-primary">
            Submit Question
        </button>

    </form>

</div>

<!-- =======================
     QUESTIONS + ANSWERS
======================= -->
<div class="card">

    <h3>My Questions & Answers</h3>

    <?php if ($my_questions->num_rows === 0): ?>

        <p style="color:#666;">
            You haven't asked any questions yet.
        </p>

    <?php else: ?>

        <?php while ($q = $my_questions->fetch_assoc()): ?>

            <div style="border-bottom:1px solid #e8f0e8; padding:16px 0;">

                <div style="display:flex; justify-content:space-between; align-items:center;">

                    <strong>Q:</strong>

                    <?php echo statusBadge($q['status']); ?>

                </div>

                <p style="margin:8px 0;">
                    <?php echo nl2br(htmlspecialchars($q['question'])); ?>
                </p>

                <p style="font-size:0.85rem; color:#888;">
                    Asked: <?php echo $q['created_at']; ?>
                </p>

                <!-- =======================
                     ANSWER FIXED SECTION
                ======================= -->
                <?php if (!empty($q['answer'])): ?>

                    <div class="alert alert-info" style="margin-top:8px;">

                        <strong>Expert Answer:</strong><br>

                        <?php echo nl2br(htmlspecialchars($q['answer'])); ?>

                    </div>

                <?php else: ?>

                    <p style="font-size:0.85rem; color:#999;">
                        Waiting for expert response...
                    </p>

                <?php endif; ?>

            </div>

        <?php endwhile; ?>

    <?php endif; ?>

</div>

<?php include("../includes/footer.php"); ?>