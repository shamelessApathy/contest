<form action="/" method="POST">
    <input type="hidden" name="context" value="daily-contest">
    <input type="hidden" name="action" value="submit-form">

    <div class="form-group required">
        <label for="name">Name</label>

        <input id="name" name="name" type="text">
    </div>

    <div class="form-group required">
        <label for="email">Email</label>

        <input id="email" name="email" type="email">
    </div>

    <div class="form-group">
        <button type="submit">Submit</button>
    </div>
</form>
